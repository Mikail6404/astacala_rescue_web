<?php

namespace App\Services;

use App\Services\AstacalaApiClient;
use Illuminate\Support\Facades\Session;
use Exception;

class AuthService
{
    protected $apiClient;

    public function __construct(AstacalaApiClient $apiClient)
    {
        $this->apiClient = $apiClient;
    }

    /**
     * Authenticate user with backend API
     */
    public function login($credentials)
    {
        try {
            $endpoint = $this->apiClient->getEndpoint('auth', 'login');
            $response = $this->apiClient->publicRequest('POST', $endpoint, $credentials);

            if ($response['success']) {
                $token = $response['data']['tokens']['accessToken'];
                $user = $response['data']['user'];

                // Store token and user data
                $this->apiClient->storeToken($token, $user);

                return [
                    'success' => true,
                    'user' => $user,
                    'token' => $token
                ];
            }

            return [
                'success' => false,
                'message' => $response['message'] ?? 'Login failed'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Register new user with backend API
     */
    public function register($userData)
    {
        try {
            $endpoint = $this->apiClient->getEndpoint('auth', 'register');
            $response = $this->apiClient->publicRequest('POST', $endpoint, $userData);

            if ($response['success']) {
                $token = $response['data']['tokens']['accessToken'];
                $user = $response['data']['user'];

                // Store token and user data
                $this->apiClient->storeToken($token, $user);

                return [
                    'success' => true,
                    'user' => $user,
                    'token' => $token
                ];
            }

            return [
                'success' => false,
                'message' => $response['message'] ?? 'Registration failed'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Logout user
     */
    public function logout()
    {
        try {
            if ($this->apiClient->isAuthenticated()) {
                $endpoint = $this->apiClient->getEndpoint('auth', 'logout');
                $this->apiClient->authenticatedRequest('POST', $endpoint);
            }

            // Clear stored token and user data
            $this->apiClient->clearStoredToken();

            return ['success' => true];
        } catch (Exception $e) {
            // Clear local data even if API call fails
            $this->apiClient->clearStoredToken();

            return [
                'success' => true,
                'message' => 'Logged out locally due to API error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get current authenticated user
     */
    public function getUser()
    {
        try {
            if (!$this->apiClient->isAuthenticated()) {
                return null;
            }

            // First try to get from stored data
            $storedUser = $this->apiClient->getStoredUser();
            if ($storedUser) {
                return $storedUser;
            }

            // If not available, fetch from API
            $endpoint = $this->apiClient->getEndpoint('auth', 'me');
            $response = $this->apiClient->authenticatedRequest('GET', $endpoint);

            if ($response['success']) {
                $user = $response['data']['user'];

                // Update stored user data
                Session::put(\config('astacala_api.jwt.user_key'), $user);

                return $user;
            }

            return null;
        } catch (Exception $e) {
            // Clear stored data if API call fails
            $this->apiClient->clearStoredToken();
            return null;
        }
    }

    /**
     * Check if user is authenticated
     */
    public function check()
    {
        return $this->apiClient->isAuthenticated() && !is_null($this->getUser());
    }

    /**
     * Get user role
     */
    public function getUserRole()
    {
        $user = $this->getUser();
        return $user['role'] ?? null;
    }

    /**
     * Check if user has specific role
     */
    public function hasRole($role)
    {
        return $this->getUserRole() === $role;
    }

    /**
     * Check if user is admin
     */
    public function isAdmin()
    {
        return in_array($this->getUserRole(), ['admin', 'super_admin']);
    }

    /**
     * Refresh authentication token
     */
    public function refreshToken()
    {
        try {
            $endpoint = $this->apiClient->getEndpoint('auth', 'refresh');
            $response = $this->apiClient->authenticatedRequest('POST', $endpoint);

            if ($response['success']) {
                $token = $response['data']['tokens']['accessToken'];
                $user = $response['data']['user'];

                // Update stored token and user data
                $this->apiClient->storeToken($token, $user);

                return ['success' => true];
            }

            return [
                'success' => false,
                'message' => $response['message'] ?? 'Token refresh failed'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}
