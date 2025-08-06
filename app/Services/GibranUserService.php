<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Log;

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
 *
 * @version 1.1.0 - Fixed CRUD operations with proper authentication
 *
 * @date August 5, 2025
 */
class GibranUserService
{
    protected $apiClient;

    protected $authService;

    public function __construct(AstacalaApiClient $apiClient, ApiAuthService $authService)
    {
        $this->apiClient = $apiClient;
        $this->authService = $authService;
    }

    /**
     * Ensure we have valid authentication before making requests
     *
     * @throws Exception If authentication fails
     */
    protected function ensureAuthenticated()
    {
        if (! $this->authService->ensureAuthenticated()) {
            throw new Exception('Failed to authenticate with backend API');
        }
    }

    /**
     * Get volunteer users for management
     *
     * @param  array  $filters  Optional filters
     * @return array Standardized response with volunteer users data
     */
    public function getVolunteerUsers($filters = [])
    {
        try {
            $endpoint = $this->apiClient->getEndpoint('users', 'volunteer_list');
            $response = $this->apiClient->authenticatedRequest('GET', $endpoint, $filters);

            if (isset($response['success']) && $response['success']) {
                return [
                    'success' => true,
                    'message' => 'Volunteer users retrieved successfully',
                    'data' => $response['data'] ?? [],
                ];
            }

            return [
                'success' => false,
                'message' => $response['message'] ?? 'Failed to load volunteer users',
                'data' => [],
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to load volunteer users: '.$e->getMessage(),
                'data' => [],
            ];
        }
    }

    /**
     * Get admin users for management
     *
     * @param  array  $filters  Optional filters
     * @return array Standardized response with admin users data
     */
    public function getAdminUsers($filters = [])
    {
        try {
            Log::info('GibranUserService: Attempting to get admin users');

            // Ensure we have valid authentication
            $this->ensureAuthenticated();

            $endpoint = $this->apiClient->getEndpoint('users', 'admin_list');
            Log::info('GibranUserService: Using endpoint', ['endpoint' => $endpoint]);

            $response = $this->apiClient->authenticatedRequest('GET', $endpoint, $filters);
            Log::info('GibranUserService: API response', ['response' => $response]);

            if (isset($response['success']) && $response['success']) {
                Log::info('GibranUserService: Admin users retrieved successfully', ['count' => count($response['data'] ?? [])]);

                return [
                    'success' => true,
                    'message' => 'Admin users retrieved successfully',
                    'data' => $response['data'] ?? [],
                ];
            }

            Log::warning('GibranUserService: API call failed', ['response' => $response]);

            return [
                'success' => false,
                'message' => $response['message'] ?? 'Failed to load admin users',
                'data' => [],
            ];
        } catch (Exception $e) {
            Log::error('GibranUserService: Exception occurred', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

            return [
                'success' => false,
                'message' => 'Failed to load admin users: '.$e->getMessage(),
                'data' => [],
            ];
        }
    }

    /**
     * Get all users for admin management
     *
     * @param  array  $filters  Optional filters (role, status, etc.)
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
                    'data' => $response['data'] ?? [],
                ];
            }

            return [
                'success' => false,
                'message' => $response['message'] ?? 'Failed to load users',
                'data' => [],
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to load users: '.$e->getMessage(),
                'data' => [],
            ];
        }
    }

    /**
     * Get a specific user by ID
     *
     * @param  int  $userId  User ID
     * @return array Standardized response with user data
     */
    public function getUser($userId)
    {
        try {
            // Ensure we have valid authentication
            $this->ensureAuthenticated();

            Log::info('GibranUserService: Getting user details', ['user_id' => $userId]);

            // First try to find user in admin list
            $endpoint = $this->apiClient->getEndpoint('users', 'admin_list');
            $response = $this->apiClient->authenticatedRequest('GET', $endpoint);

            if (isset($response['success']) && $response['success']) {
                $allUsers = $response['data'] ?? [];

                // Find the specific user by ID in admin list
                $userData = null;
                foreach ($allUsers as $user) {
                    if ($user['id'] == $userId) {
                        $userData = $user;
                        break;
                    }
                }

                if ($userData) {
                    // Map API fields to web form fields for compatibility
                    $userData = $this->mapUserDataFromApi($userData);

                    Log::info('GibranUserService: User found in admin list and mapped', ['user_id' => $userId, 'mapped_fields' => array_keys($userData)]);

                    return [
                        'success' => true,
                        'message' => 'User retrieved successfully',
                        'data' => $userData,
                    ];
                }
            }

            // If not found in admin list, try volunteer list
            Log::info('GibranUserService: User not found in admin list, trying volunteer list', ['user_id' => $userId]);

            $volunteerEndpoint = $this->apiClient->getEndpoint('users', 'volunteer_list');
            $volunteerResponse = $this->apiClient->authenticatedRequest('GET', $volunteerEndpoint);

            if (isset($volunteerResponse['success']) && $volunteerResponse['success']) {
                $allVolunteers = $volunteerResponse['data'] ?? [];

                // Find the specific user by ID in volunteer list
                $userData = null;
                foreach ($allVolunteers as $user) {
                    if ($user['id'] == $userId) {
                        $userData = $user;
                        break;
                    }
                }

                if ($userData) {
                    // Map API fields to web form fields for compatibility
                    $userData = $this->mapUserDataFromApi($userData);

                    Log::info('GibranUserService: User found in volunteer list and mapped', ['user_id' => $userId, 'mapped_fields' => array_keys($userData)]);

                    return [
                        'success' => true,
                        'message' => 'User retrieved successfully',
                        'data' => $userData,
                    ];
                }
            }

            Log::warning('GibranUserService: User not found in either admin or volunteer list', ['user_id' => $userId]);

            return [
                'success' => false,
                'message' => 'User not found',
            ];
        } catch (Exception $e) {
            Log::error('GibranUserService: Get user exception', ['user_id' => $userId, 'error' => $e->getMessage()]);

            return [
                'success' => false,
                'message' => 'Failed to load user: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Create a new user (admin function)
     *
     * @param  array  $userData  User data
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
                    'data' => $response['data'] ?? [],
                ];
            }

            return [
                'success' => false,
                'message' => $response['message'] ?? 'Failed to create user',
                'errors' => $response['errors'] ?? null,
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to create user: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Update a user (admin function)
     *
     * @param  int  $userId  User ID to update
     * @param  array  $userData  Updated user data
     * @return array Standardized response
     */
    public function updateUser($userId, $userData)
    {
        try {
            // Ensure we have valid authentication
            $this->ensureAuthenticated();

            // Map web form fields to backend API fields
            $mappedData = $this->mapUserDataForApi($userData);

            Log::info('GibranUserService: Updating user', ['user_id' => $userId, 'mapped_data' => $mappedData]);

            // Use the new admin endpoint for updating user by ID
            $endpoint = $this->apiClient->getEndpoint('users', 'update_user_by_id', ['id' => $userId]);
            $response = $this->apiClient->authenticatedRequest('PUT', $endpoint, $mappedData);

            if (isset($response['success']) && $response['success']) {
                Log::info('GibranUserService: User updated successfully', ['user_id' => $userId]);

                return [
                    'success' => true,
                    'message' => 'User updated successfully',
                    'data' => $response['data'] ?? [],
                ];
            }

            Log::warning('GibranUserService: User update failed', ['user_id' => $userId, 'response' => $response]);

            return [
                'success' => false,
                'message' => $response['message'] ?? 'Failed to update user',
            ];
        } catch (Exception $e) {
            Log::error('GibranUserService: User update exception', ['user_id' => $userId, 'error' => $e->getMessage()]);

            return [
                'success' => false,
                'message' => 'Failed to update user: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Delete a user (admin function) - HARD DELETE
     *
     * @param  int  $userId  User ID to delete
     * @return array Standardized response
     */
    public function deleteUser($userId)
    {
        try {
            // Ensure we have valid authentication
            $this->ensureAuthenticated();

            Log::info('GibranUserService: Hard deleting user', ['user_id' => $userId]);

            // Use hard delete endpoint instead of deactivation
            $endpoint = $this->apiClient->getEndpoint('users', 'delete_user_by_id', ['id' => $userId]);
            $response = $this->apiClient->authenticatedRequest('DELETE', $endpoint);

            if (isset($response['success']) && $response['success']) {
                Log::info('GibranUserService: User deleted successfully', ['user_id' => $userId]);

                return [
                    'success' => true,
                    'message' => 'User deleted successfully',
                    'data' => $response['data'] ?? [],
                ];
            }

            Log::warning('GibranUserService: User deletion failed', ['user_id' => $userId, 'response' => $response]);

            return [
                'success' => false,
                'message' => $response['message'] ?? 'Failed to delete user',
            ];
        } catch (Exception $e) {
            Log::error('GibranUserService: User deletion exception', ['user_id' => $userId, 'error' => $e->getMessage()]);

            return [
                'success' => false,
                'message' => 'Failed to delete user: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Map web form user data to backend API field names
     *
     * @param  array  $userData  Raw user data from web form
     * @return array Mapped data for API
     */
    protected function mapUserDataForApi($userData)
    {
        $mapping = [
            // Web form field => API field
            'nama_lengkap' => 'name',
            // Removed email mapping - backend doesn't allow email updates for security
            'tanggal_lahir' => 'birth_date',
            'tempat_lahir' => 'place_of_birth',
            'no_handphone' => 'phone',
            'no_anggota' => 'member_number',
            'alamat' => 'address',
            'organisasi' => 'organization',
            'role' => 'role',
            'status' => 'is_active',

            // Admin-specific field mappings
            'nama_lengkap_admin' => 'name',
            // Removed username_akun_admin mapping - backend doesn't allow email/username updates for security
            'tanggal_lahir_admin' => 'birth_date',
            'tempat_lahir_admin' => 'place_of_birth',
            'no_handphone_admin' => 'phone',
            'password_akun_admin' => 'password',

            // Volunteer-specific field mappings (from Datapengguna page)
            'nama_lengkap_pengguna' => 'name',
            'tanggal_lahir_pengguna' => 'birth_date',
            'tempat_lahir_pengguna' => 'place_of_birth',
            'no_handphone_pengguna' => 'phone',
            'username_akun_pengguna' => 'email',  // Volunteer username maps to email
            'password_akun_pengguna' => 'password',

            // Handle both old and new field names
            'name' => 'name',
            'birth_date' => 'birth_date',
            'place_of_birth' => 'place_of_birth',
            'phone' => 'phone',
            'member_number' => 'member_number',
            'address' => 'address',
            'organization' => 'organization',
            'is_active' => 'is_active',
        ];

        $mappedData = [];

        foreach ($userData as $key => $value) {
            if (isset($mapping[$key])) {
                $apiKey = $mapping[$key];

                // Handle status conversion
                if ($apiKey === 'is_active' && ! is_bool($value)) {
                    if (in_array(strtolower($value), ['active', '1', 'true', 'yes'])) {
                        $value = true;
                    } else {
                        $value = false;
                    }
                }

                $mappedData[$apiKey] = $value;
            }
        }

        // Ensure required fields are set with defaults if missing
        if (! isset($mappedData['name']) && ! empty($userData['nama_lengkap'])) {
            $mappedData['name'] = $userData['nama_lengkap'];
        }

        Log::info('GibranUserService: Field mapping completed', ['original' => $userData, 'mapped' => $mappedData]);

        return $mappedData;
    }

    /**
     * Map API user data to web form field names
     *
     * @param  array  $apiData  User data from API
     * @return array Mapped data for web forms
     */
    protected function mapUserDataFromApi($apiData)
    {
        $mapping = [
            // API field => Web form field (for both admin and regular users)
            'name' => 'nama_lengkap',
            'email' => 'email',
            'birth_date' => 'tanggal_lahir',
            'place_of_birth' => 'tempat_lahir',
            'phone' => 'no_handphone',
            'member_number' => 'no_anggota',
            'address' => 'alamat',
            'organization' => 'organisasi',
            'role' => 'role',
            'is_active' => 'status',
            'id' => 'id',
            'created_at' => 'created_at',
            'last_login' => 'last_login',
            'isActive' => 'status', // Alternative field name from single user endpoint

            // Admin-specific field mappings (for TICKET #005 - Issue 5b fix)
            'username' => 'username_akun_admin',
            'name' => 'nama_lengkap_admin',
            'birth_date' => 'tanggal_lahir_admin',
            'place_of_birth' => 'tempat_lahir_admin',
            'phone' => 'no_handphone_admin',
            'member_number' => 'no_anggota',
        ];

        $mappedData = [];

        // First, add all original API data
        foreach ($apiData as $key => $value) {
            $mappedData[$key] = $value;
        }

        // Then add mapped fields for web form compatibility
        foreach ($apiData as $key => $value) {
            if (isset($mapping[$key])) {
                $webKey = $mapping[$key];

                // Handle status conversion
                if ($webKey === 'status') {
                    if (is_bool($value)) {
                        $value = $value ? 'active' : 'inactive';
                    } elseif ($value === 1 || $value === '1') {
                        $value = 'active';
                    } else {
                        $value = 'inactive';
                    }
                }

                $mappedData[$webKey] = $value;
            }
        }

        // Add admin-specific mappings if this is admin data
        if (isset($apiData['role']) && in_array(strtolower($apiData['role']), ['admin', 'super_admin'])) {
            $mappedData['username_akun_admin'] = $apiData['email'] ?? $apiData['username'] ?? '';
            $mappedData['nama_lengkap_admin'] = $apiData['name'] ?? '';
            $mappedData['tanggal_lahir_admin'] = $apiData['birth_date'] ?? '';
            $mappedData['tempat_lahir_admin'] = $apiData['place_of_birth'] ?? '';
            $mappedData['no_handphone_admin'] = $apiData['phone'] ?? '';
            $mappedData['no_anggota'] = $apiData['member_number'] ?? '';
            $mappedData['password_akun_admin'] = '****'; // Never show real password
        }

        // Add volunteer-specific mappings if this is volunteer data
        if (isset($apiData['role']) && in_array(strtolower($apiData['role']), ['volunteer', 'user'])) {
            $mappedData['username_akun_pengguna'] = $apiData['email'] ?? $apiData['username'] ?? '';
            $mappedData['nama_lengkap_pengguna'] = $apiData['name'] ?? '';
            $mappedData['tanggal_lahir_pengguna'] = $apiData['birth_date'] ?? '';
            $mappedData['tempat_lahir_pengguna'] = $apiData['place_of_birth'] ?? '';
            $mappedData['no_handphone_pengguna'] = $apiData['phone'] ?? '';
            $mappedData['password_akun_pengguna'] = '****'; // Never show real password
        }

        Log::info('GibranUserService: API to Web mapping completed', [
            'api_fields' => array_keys($apiData),
            'web_fields' => array_keys($mappedData),
        ]);

        return $mappedData;
    }

    /**
     * Update user role (admin function)
     *
     * @param  int  $userId  User ID
     * @param  string  $role  New role (ADMIN, VOLUNTEER, etc.)
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
                    'data' => $response['data'] ?? [],
                ];
            }

            return [
                'success' => false,
                'message' => $response['message'] ?? 'Failed to update user role',
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to update user role: '.$e->getMessage(),
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
                    'data' => $response['data'] ?? [],
                ];
            }

            return [
                'success' => false,
                'message' => $response['message'] ?? 'Failed to load user statistics',
                'data' => [],
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to load user statistics: '.$e->getMessage(),
                'data' => [],
            ];
        }
    }

    /**
     * Search users by various criteria
     *
     * @param  array  $searchCriteria  Search parameters
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
                    'data' => $response['data'] ?? [],
                ];
            }

            return [
                'success' => false,
                'message' => $response['message'] ?? 'Search failed',
                'data' => [],
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Search failed: '.$e->getMessage(),
                'data' => [],
            ];
        }
    }
}
