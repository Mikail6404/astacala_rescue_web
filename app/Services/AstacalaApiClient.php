<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Exception;

class AstacalaApiClient
{
    protected $baseUrl;
    protected $version;
    protected $timeout;
    protected $retryAttempts;
    protected $headers;

    public function __construct()
    {
        $this->baseUrl = Config::get('astacala_api.base_url');
        $this->version = Config::get('astacala_api.version');
        $this->timeout = Config::get('astacala_api.timeout');
        $this->retryAttempts = Config::get('astacala_api.retry_attempts');
        $this->headers = Config::get('astacala_api.headers');
    }

    /**
     * Make authenticated API request
     */
    public function authenticatedRequest($method, $endpoint, $data = [], $files = [])
    {
        $token = $this->getStoredToken();

        if (!$token) {
            throw new Exception('No authentication token found. Please login first.');
        }

        $headers = array_merge($this->headers, [
            'Authorization' => 'Bearer ' . $token
        ]);

        return $this->makeRequest($method, $endpoint, $data, $headers, $files);
    }

    /**
     * Make public API request (no authentication)
     */
    public function publicRequest($method, $endpoint, $data = [])
    {
        return $this->makeRequest($method, $endpoint, $data, $this->headers);
    }

    /**
     * Make the actual HTTP request
     */
    protected function makeRequest($method, $endpoint, $data = [], $headers = [], $files = [])
    {
        $url = $this->buildUrl($endpoint);

        try {
            $http = Http::withHeaders($headers)
                ->timeout($this->timeout)
                ->retry($this->retryAttempts);

            // Handle file uploads
            if (!empty($files)) {
                foreach ($files as $key => $file) {
                    $http->attach($key, $file->get(), $file->getClientOriginalName());
                }
            }

            $response = match (strtoupper($method)) {
                'GET' => $http->get($url, $data),
                'POST' => $http->post($url, $data),
                'PUT' => $http->put($url, $data),
                'PATCH' => $http->patch($url, $data),
                'DELETE' => $http->delete($url, $data),
                default => throw new Exception("Unsupported HTTP method: {$method}")
            };

            // Log API calls for debugging
            Log::info('API Request', [
                'method' => $method,
                'url' => $url,
                'status' => $response->status(),
                'headers' => $headers
            ]);

            return $this->handleResponse($response);
        } catch (Exception $e) {
            Log::error('API Request Failed', [
                'method' => $method,
                'url' => $url,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Handle API response
     */
    protected function handleResponse($response)
    {
        if ($response->successful()) {
            // Return the API response directly without extra wrapping
            return $response->json();
        }

        // Handle authentication errors
        if ($response->status() === 401) {
            $this->clearStoredToken();
            return [
                'success' => false,
                'message' => 'Authentication failed. Please login again.',
                'status_code' => 401
            ];
        }

        // Handle other errors
        $error = $response->json('message') ?? 'API request failed';
        return [
            'success' => false,
            'message' => $error,
            'status_code' => $response->status()
        ];
    }

    /**
     * Build full URL for endpoint
     */
    protected function buildUrl($endpoint)
    {
        // Replace version placeholder
        if (!empty($this->version)) {
            $endpoint = str_replace('{version}', $this->version, $endpoint);
        } else {
            // Remove the version placeholder and clean up double slashes
            $endpoint = str_replace('/{version}', '', $endpoint);
        }

        return rtrim($this->baseUrl, '/') . $endpoint;
    }

    /**
     * Get endpoint URL from config
     */
    public function getEndpoint($category, $action, $params = [])
    {
        $endpoints = config('astacala_api.endpoints');

        if (!isset($endpoints[$category][$action])) {
            throw new Exception("Endpoint not found: {$category}.{$action}");
        }

        $endpoint = $endpoints[$category][$action];

        // Replace version placeholder
        $endpoint = str_replace('{version}', $this->version, $endpoint);

        // Replace parameters in URL
        foreach ($params as $key => $value) {
            $endpoint = str_replace("{{$key}}", $value, $endpoint);
        }

        return $endpoint;
    }

    /**
     * Store JWT token in session
     */
    public function storeToken($token, $user = null)
    {
        Session::put(config('astacala_api.jwt.storage_key'), $token);

        if ($user) {
            Session::put(config('astacala_api.jwt.user_key'), $user);
        }
    }

    /**
     * Get stored JWT token
     */
    public function getStoredToken()
    {
        return Session::get(config('astacala_api.jwt.storage_key'));
    }

    /**
     * Clear stored token and user data
     */
    public function clearStoredToken()
    {
        Session::forget(config('astacala_api.jwt.storage_key'));
        Session::forget(config('astacala_api.jwt.user_key'));
    }

    /**
     * Get stored user data
     */
    public function getStoredUser()
    {
        return Session::get(config('astacala_api.jwt.user_key'));
    }

    /**
     * Check if user is authenticated
     */
    public function isAuthenticated()
    {
        return !is_null($this->getStoredToken());
    }
}
