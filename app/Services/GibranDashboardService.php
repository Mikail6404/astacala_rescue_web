<?php

namespace App\Services;

use App\Services\AstacalaApiClient;
use Exception;

/**
 * Gibran Dashboard Service
 * 
 * Handles dashboard-related operations for the web application using
 * the /api/gibran/dashboard/* endpoints from the unified backend.
 * 
 * This service provides web admin dashboard functionality including
 * statistics, analytics, and overview data.
 * 
 * @author Web Integration Team
 * @version 1.0.0
 * @date August 3, 2025
 */
class GibranDashboardService
{
    protected $apiClient;

    public function __construct(AstacalaApiClient $apiClient)
    {
        $this->apiClient = $apiClient;
    }

    /**
     * Get dashboard statistics for admin overview
     * 
     * @param array $filters Optional date range or other filters
     * @return array Dashboard statistics data
     */
    public function getStatistics($filters = [])
    {
        try {
            $endpoint = $this->apiClient->getEndpoint('gibran', 'dashboard_statistics');
            $response = $this->apiClient->authenticatedRequest('GET', $endpoint, $filters);

            // Check for successful response in gibran format
            if (isset($response['status']) && $response['status'] === 'success') {
                return [
                    'success' => true,
                    'message' => $response['message'] ?? 'Dashboard statistics retrieved successfully',
                    'data' => $response['data'] ?? []
                ];
            }

            return [
                'success' => false,
                'message' => $response['message'] ?? 'Failed to load dashboard statistics',
                'data' => $this->getDefaultStatistics() // Fallback to default stats
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to load dashboard statistics: ' . $e->getMessage(),
                'data' => $this->getDefaultStatistics() // Fallback to default stats
            ];
        }
    }

    /**
     * Get news/berita bencana data for admin management
     * 
     * @return array News data
     */
    public function getBeritaBencana()
    {
        try {
            $endpoint = $this->apiClient->getEndpoint('gibran', 'berita_bencana');
            $response = $this->apiClient->authenticatedRequest('GET', $endpoint);

            if (isset($response['status']) && $response['status'] === 'success') {
                return [
                    'success' => true,
                    'message' => $response['message'] ?? 'News data retrieved successfully',
                    'data' => $response['data'] ?? []
                ];
            }

            return [
                'success' => false,
                'message' => $response['message'] ?? 'Failed to load news data',
                'data' => []
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to load news data: ' . $e->getMessage(),
                'data' => []
            ];
        }
    }

    /**
     * Get system overview data for dashboard
     * 
     * @return array System overview data
     */
    public function getSystemOverview()
    {
        try {
            // Combine multiple data sources for comprehensive overview
            $statistics = $this->getStatistics();
            $recentReports = $this->getRecentReports();
            $userActivity = $this->getUserActivity();

            return [
                'success' => true,
                'message' => 'System overview retrieved successfully',
                'data' => [
                    'statistics' => $statistics['data'] ?? [],
                    'recent_reports' => $recentReports['data'] ?? [],
                    'user_activity' => $userActivity['data'] ?? []
                ]
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to load system overview: ' . $e->getMessage(),
                'data' => []
            ];
        }
    }

    /**
     * Get recent reports for dashboard display
     * 
     * @param int $limit Number of recent reports to fetch
     * @return array Recent reports data
     */
    protected function getRecentReports($limit = 5)
    {
        try {
            $endpoint = $this->apiClient->getEndpoint('gibran', 'pelaporans_list');
            $response = $this->apiClient->authenticatedRequest('GET', $endpoint, [
                'limit' => $limit,
                'order' => 'created_at',
                'direction' => 'desc'
            ]);

            if (isset($response['success']) && $response['success']) {
                return [
                    'success' => true,
                    'data' => array_slice($response['data'] ?? [], 0, $limit)
                ];
            }

            return [
                'success' => false,
                'message' => 'Failed to load recent reports',
                'data' => []
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to load recent reports: ' . $e->getMessage(),
                'data' => []
            ];
        }
    }

    /**
     * Get user activity data for dashboard
     * 
     * @return array User activity data
     */
    protected function getUserActivity()
    {
        try {
            // This would use user statistics endpoint if available
            $endpoint = $this->apiClient->getEndpoint('users', 'statistics');
            $response = $this->apiClient->authenticatedRequest('GET', $endpoint);

            if (isset($response['success']) && $response['success']) {
                return [
                    'success' => true,
                    'data' => $response['data'] ?? []
                ];
            }

            return [
                'success' => false,
                'message' => 'Failed to load user activity',
                'data' => []
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to load user activity: ' . $e->getMessage(),
                'data' => []
            ];
        }
    }

    /**
     * Get default statistics structure for fallback
     * 
     * @return array Default statistics
     */
    protected function getDefaultStatistics()
    {
        return [
            'total_reports' => 0,
            'pending_reports' => 0,
            'verified_reports' => 0,
            'total_users' => 0,
            'active_users' => 0,
            'recent_activity' => []
        ];
    }
}
