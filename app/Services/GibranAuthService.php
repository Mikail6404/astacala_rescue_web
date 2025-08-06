<?php

namespace App\Services;

use Exception;

/**
 * Gibran Authentication Service
 *
 * Handles authentication specifically for the web application using
 * the /api/gibran/auth/* endpoints from the unified backend.
 *
 * This service provides web-specific authentication that integrates
 * with the cross-platform user management system.
 *
 * @author Web Integration Team
 *
 * @version 1.0.0
 *
 * @date August 3, 2025
 */
class GibranAuthService
{
    protected $apiClient;

    public function __construct(AstacalaApiClient $apiClient)
    {
        $this->apiClient = $apiClient;
    }

    /**
     * Authenticate admin user using Gibran-specific endpoint
     *
     * @param  array  $credentials  Login credentials (email, password)
     * @return array Standardized response with success/error status
     */
    public function login($credentials)
    {
        try {
            $endpoint = $this->apiClient->getEndpoint('gibran', 'auth_login');
            $response = $this->apiClient->publicRequest('POST', $endpoint, $credentials);

            // Check for successful authentication (unified backend returns status: "success")
            if (isset($response['status']) && $response['status'] === 'success') {
                // Extract token and user data from unified backend response format
                $token = $response['data']['access_token'] ?? null;
                $user = $response['data']['user'] ?? null;

                if ($token && $user) {
                    // Store authentication data in session
                    $this->apiClient->storeToken($token, $user);

                    return [
                        'success' => true,
                        'message' => $response['message'] ?? 'Login successful',
                        'user' => $user,
                        'token' => $token,
                    ];
                }
            }

            // Extract error message from response
            $errorMessage = $response['message'] ?? 'Login failed - invalid credentials';
            if (isset($response['error'])) {
                $errorMessage .= ' ('.$response['error'].')';
            }

            return [
                'success' => false,
                'message' => $errorMessage,
                'error' => $response['error'] ?? null,
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Login failed: '.$e->getMessage(),
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Logout current user and clear session
     *
     * @return array Response status
     */
    public function logout()
    {
        try {
            // Clear local session data
            $this->apiClient->clearStoredToken();

            return [
                'success' => true,
                'message' => 'Logout successful',
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Logout failed: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Check if user is currently authenticated
     *
     * @return bool Authentication status
     */
    public function isAuthenticated()
    {
        return $this->apiClient->isAuthenticated();
    }

    /**
     * Get current authenticated user data
     *
     * @return array|null User data or null if not authenticated
     */
    public function getUser()
    {
        return $this->apiClient->getStoredUser();
    }

    /**
     * Get current authentication token
     *
     * @return string|null JWT token or null if not authenticated
     */
    public function getToken()
    {
        return $this->apiClient->getStoredToken();
    }

    /**
     * Register a new admin user
     *
     * @param  array  $userData  User registration data
     * @return array Standardized response
     */
    public function register($userData)
    {
        try {
            // Use general registration endpoint since Gibran may not have specific register endpoint
            $endpoint = $this->apiClient->getEndpoint('auth', 'register');
            $response = $this->apiClient->publicRequest('POST', $endpoint, $userData);

            if (isset($response['success']) && $response['success']) {
                return [
                    'success' => true,
                    'message' => 'Registration successful',
                    'data' => $response['data'] ?? [],
                ];
            }

            return [
                'success' => false,
                'message' => $response['message'] ?? 'Registration failed',
                'errors' => $response['errors'] ?? null,
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Registration failed: '.$e->getMessage(),
            ];
        }
    }
}
