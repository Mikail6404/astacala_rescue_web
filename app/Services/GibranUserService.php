<?php

namespace App\Services;

use App\Services\AstacalaApiClient;
use Exception;

/**
 * Gibran User Service
 * 
 * Handles user management operations for the web application using
 * the unified backend API instead of local database queries.
 * 
 * This service provides web admin functionality for viewing, managing,
 * and administering users across both mobile and web platforms.
 * 
 * @author Web Integration Team
 * @version 1.0.0
 * @date August 3, 2025
 */
class GibranUserService
{
    protected $apiClient;

    public function __construct(AstacalaApiClient $apiClient)
    {
        $this->apiClient = $apiClient;
    }

    /**
     * Get all users for admin management
     * 
     * @param array $filters Optional filters (role, status, etc.)
     * @return array Standardized response with users data
     */
    public function getAllUsers($filters = [])
    {
        try {
            $endpoint = $this->apiClient->getEndpoint('users', 'admin_list');
            $response = $this->apiClient->authenticatedRequest('GET', $endpoint, $filters);

            if (isset($response['success']) && $response['success']) {
                return [
                    'success' => true,
                    'message' => 'Users retrieved successfully',
                    'data' => $response['data'] ?? []
                ];
            }

            return [
                'success' => false,
                'message' => $response['message'] ?? 'Failed to load users',
                'data' => []
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to load users: ' . $e->getMessage(),
                'data' => []
            ];
        }
    }

    /**
     * Get a specific user by ID
     * 
     * @param int $userId User ID
     * @return array Standardized response with user data
     */
    public function getUser($userId)
    {
        try {
            $endpoint = $this->apiClient->getEndpoint('users', 'profile');
            $response = $this->apiClient->authenticatedRequest('GET', $endpoint, ['user_id' => $userId]);

            if (isset($response['success']) && $response['success']) {
                return [
                    'success' => true,
                    'message' => 'User retrieved successfully',
                    'data' => $response['data'] ?? null
                ];
            }

            return [
                'success' => false,
                'message' => $response['message'] ?? 'User not found'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to load user: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Create a new user (admin function)
     * 
     * @param array $userData User data
     * @return array Standardized response
     */
    public function createUser($userData)
    {
        try {
            $endpoint = $this->apiClient->getEndpoint('users', 'create_admin');
            $response = $this->apiClient->authenticatedRequest('POST', $endpoint, $userData);

            if (isset($response['success']) && $response['success']) {
                return [
                    'success' => true,
                    'message' => 'User created successfully',
                    'data' => $response['data'] ?? []
                ];
            }

            return [
                'success' => false,
                'message' => $response['message'] ?? 'Failed to create user',
                'errors' => $response['errors'] ?? null
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to create user: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Update a user (admin function)
     * 
     * @param int $userId User ID to update
     * @param array $userData Updated user data
     * @return array Standardized response
     */
    public function updateUser($userId, $userData)
    {
        try {
            $endpoint = $this->apiClient->getEndpoint('users', 'update_profile');
            $response = $this->apiClient->authenticatedRequest('PUT', $endpoint, array_merge($userData, ['user_id' => $userId]));

            if (isset($response['success']) && $response['success']) {
                return [
                    'success' => true,
                    'message' => 'User updated successfully',
                    'data' => $response['data'] ?? []
                ];
            }

            return [
                'success' => false,
                'message' => $response['message'] ?? 'Failed to update user'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to update user: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Delete a user (admin function)
     * 
     * @param int $userId User ID to delete
     * @return array Standardized response
     */
    public function deleteUser($userId)
    {
        try {
            // Note: Using update_status endpoint to deactivate instead of hard delete
            $endpoint = $this->apiClient->getEndpoint('users', 'update_status', ['id' => $userId]);
            $response = $this->apiClient->authenticatedRequest('PUT', $endpoint, ['status' => 'inactive']);

            if (isset($response['success']) && $response['success']) {
                return [
                    'success' => true,
                    'message' => 'User deactivated successfully'
                ];
            }

            return [
                'success' => false,
                'message' => $response['message'] ?? 'Failed to deactivate user'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to deactivate user: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Update user role (admin function)
     * 
     * @param int $userId User ID
     * @param string $role New role (ADMIN, VOLUNTEER, etc.)
     * @return array Standardized response
     */
    public function updateUserRole($userId, $role)
    {
        try {
            $endpoint = $this->apiClient->getEndpoint('users', 'update_role', ['id' => $userId]);
            $response = $this->apiClient->authenticatedRequest('PUT', $endpoint, ['role' => $role]);

            if (isset($response['success']) && $response['success']) {
                return [
                    'success' => true,
                    'message' => 'User role updated successfully',
                    'data' => $response['data'] ?? []
                ];
            }

            return [
                'success' => false,
                'message' => $response['message'] ?? 'Failed to update user role'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to update user role: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get user statistics for dashboard
     * 
     * @return array Statistics data
     */
    public function getUserStatistics()
    {
        try {
            $endpoint = $this->apiClient->getEndpoint('users', 'statistics');
            $response = $this->apiClient->authenticatedRequest('GET', $endpoint);

            if (isset($response['success']) && $response['success']) {
                return [
                    'success' => true,
                    'message' => 'User statistics retrieved successfully',
                    'data' => $response['data'] ?? []
                ];
            }

            return [
                'success' => false,
                'message' => $response['message'] ?? 'Failed to load user statistics',
                'data' => []
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to load user statistics: ' . $e->getMessage(),
                'data' => []
            ];
        }
    }

    /**
     * Search users by various criteria
     * 
     * @param array $searchCriteria Search parameters
     * @return array Standardized response with search results
     */
    public function searchUsers($searchCriteria)
    {
        try {
            $endpoint = $this->apiClient->getEndpoint('users', 'admin_list');
            $response = $this->apiClient->authenticatedRequest('GET', $endpoint, $searchCriteria);

            if (isset($response['success']) && $response['success']) {
                return [
                    'success' => true,
                    'message' => 'Search completed successfully',
                    'data' => $response['data'] ?? []
                ];
            }

            return [
                'success' => false,
                'message' => $response['message'] ?? 'Search failed',
                'data' => []
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Search failed: ' . $e->getMessage(),
                'data' => []
            ];
        }
    }
}
