<?php

namespace App\Services;

use App\Services\AstacalaApiClient;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * Authentication Service for Backend API Integration
 * 
 * Handles authentication with the unified backend API system.
 * Manages JWT tokens and user sessions for web application.
 */
class ApiAuthService
{
    protected $apiClient;

    public function __construct(AstacalaApiClient $apiClient)
    {
        $this->apiClient = $apiClient;
    }

    /**
     * Login to backend API and store authentication token
     * 
     * @param string $email
     * @param string $password  
     * @return array Response with success status and data
     */
    public function login($email, $password)
    {
        try {
            $endpoint = $this->apiClient->getEndpoint('auth', 'login');

            $loginData = [
                'email' => $email,
                'password' => $password
            ];

            $response = $this->apiClient->publicRequest('POST', $endpoint, $loginData);

            if (isset($response['success']) && $response['success'] && isset($response['data']['tokens']['accessToken'])) {
                $token = $response['data']['tokens']['accessToken'];
                $user = $response['data']['user'];

                // Store token and user data in session
                $this->apiClient->storeToken($token, $user);

                Log::info('ApiAuthService: Login successful', ['user_id' => $user['id'], 'email' => $email]);

                return [
                    'success' => true,
                    'message' => 'Login successful',
                    'user' => $user,
                    'token' => $token
                ];
            }

            Log::warning('ApiAuthService: Login failed', ['email' => $email, 'response' => $response]);
            return [
                'success' => false,
                'message' => $response['message'] ?? 'Invalid credentials'
            ];
        } catch (Exception $e) {
            Log::error('ApiAuthService: Login exception', ['email' => $email, 'error' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => 'Authentication failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Logout from backend API and clear stored data
     * 
     * @return array Response with success status
     */
    public function logout()
    {
        try {
            $endpoint = $this->apiClient->getEndpoint('auth', 'logout');

            // Try to logout from backend API
            $response = $this->apiClient->authenticatedRequest('POST', $endpoint);

            // Clear local session data regardless of API response
            $this->apiClient->clearStoredToken();

            return [
                'success' => true,
                'message' => 'Logout successful'
            ];
        } catch (Exception $e) {
            // Clear local session even if API logout fails
            $this->apiClient->clearStoredToken();

            Log::error('ApiAuthService: Logout exception', ['error' => $e->getMessage()]);
            return [
                'success' => true,
                'message' => 'Logout completed'
            ];
        }
    }

    /**
     * Check if user is authenticated
     * 
     * @return bool
     */
    public function isAuthenticated()
    {
        return !empty($this->apiClient->getStoredToken());
    }

    /**
     * Get current authenticated user data
     * 
     * @return array|null
     */
    public function getUser()
    {
        return $this->apiClient->getStoredUser();
    }

    /**
     * Initialize backend API authentication if needed
     * Uses test credentials for now - should be replaced with proper auth flow
     * 
     * @return bool Success status
     */
    public function ensureAuthenticated()
    {
        if ($this->isAuthenticated()) {
            return true;
        }

        Log::info('ApiAuthService: No authentication found, attempting to login');

        // For now, use test admin credentials
        // TODO: Replace with proper authentication flow from user session
        $result = $this->login('test-admin@astacala.test', 'testpassword123');

        if ($result['success']) {
            Log::info('ApiAuthService: Auto-authentication successful');
            return true;
        }

        Log::error('ApiAuthService: Auto-authentication failed', ['result' => $result]);
        return false;
    }

    /**
     * Force refresh authentication token
     * 
     * @return bool Success status
     */
    public function refreshToken()
    {
        try {
            $endpoint = $this->apiClient->getEndpoint('auth', 'refresh');
            $response = $this->apiClient->authenticatedRequest('POST', $endpoint);

            if (isset($response['success']) && $response['success'] && isset($response['data']['tokens']['accessToken'])) {
                $token = $response['data']['tokens']['accessToken'];
                $user = $response['data']['user'];

                $this->apiClient->storeToken($token, $user);

                Log::info('ApiAuthService: Token refresh successful');
                return true;
            }

            Log::warning('ApiAuthService: Token refresh failed', ['response' => $response]);
            return false;
        } catch (Exception $e) {
            Log::error('ApiAuthService: Token refresh exception', ['error' => $e->getMessage()]);
            return false;
        }
    }
}
