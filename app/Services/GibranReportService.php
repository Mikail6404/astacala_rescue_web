<?php

namespace App\Services;

use App\Services\AstacalaApiClient;
use Exception;

/**
 * Gibran Report Service
 * 
 * Handles disaster report operations for the web application using
 * the /api/gibran/pelaporans/* endpoints from the unified backend.
 * 
 * This service provides web admin functionality for viewing, managing,
 * and verifying disaster reports submitted through the mobile app.
 * 
 * @author Web Integration Team
 * @version 1.0.0
 * @date August 3, 2025
 */
class GibranReportService
{
    protected $apiClient;

    public function __construct(AstacalaApiClient $apiClient)
    {
        $this->apiClient = $apiClient;
    }

    /**
     * Get all disaster reports for admin review
     * 
     * @param array $filters Optional filters (status, date_range, etc.)
     * @return array Standardized response with reports data
     */
    public function getAllReports($filters = [])
    {
        try {
            $endpoint = $this->apiClient->getEndpoint('gibran', 'pelaporans_list');
            $response = $this->apiClient->authenticatedRequest('GET', $endpoint, $filters);

            if (isset($response['success']) && $response['success']) {
                return [
                    'success' => true,
                    'message' => 'Reports retrieved successfully',
                    'data' => $response['data'] ?? []
                ];
            }

            return [
                'success' => false,
                'message' => $response['message'] ?? 'Failed to load reports',
                'data' => []
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to load reports: ' . $e->getMessage(),
                'data' => []
            ];
        }
    }

    /**
     * Create a new disaster report (for web admin use)
     * 
     * @param array $reportData Report data
     * @param array $files Optional file uploads
     * @return array Standardized response
     */
    public function createReport($reportData, $files = [])
    {
        try {
            $endpoint = $this->apiClient->getEndpoint('gibran', 'pelaporans_create');
            $response = $this->apiClient->authenticatedRequest('POST', $endpoint, $reportData, $files);

            if (isset($response['success']) && $response['success']) {
                return [
                    'success' => true,
                    'message' => 'Report created successfully',
                    'data' => $response['data'] ?? []
                ];
            }

            return [
                'success' => false,
                'message' => $response['message'] ?? 'Failed to create report',
                'errors' => $response['errors'] ?? null
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to create report: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Verify a disaster report (admin action)
     * 
     * @param int $reportId Report ID to verify
     * @param array $verificationData Verification status and notes
     * @return array Standardized response
     */
    public function verifyReport($reportId, $verificationData = [])
    {
        try {
            $endpoint = $this->apiClient->getEndpoint('gibran', 'pelaporans_verify', ['id' => $reportId]);
            $response = $this->apiClient->authenticatedRequest('POST', $endpoint, $verificationData);

            if (isset($response['success']) && $response['success']) {
                return [
                    'success' => true,
                    'message' => 'Report verification updated successfully',
                    'data' => $response['data'] ?? []
                ];
            }

            return [
                'success' => false,
                'message' => $response['message'] ?? 'Failed to verify report'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to verify report: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get report statistics for dashboard
     * 
     * @return array Statistics data
     */
    public function getReportStatistics()
    {
        try {
            $endpoint = $this->apiClient->getEndpoint('gibran', 'dashboard_statistics');
            $response = $this->apiClient->authenticatedRequest('GET', $endpoint);

            if (isset($response['success']) && $response['success']) {
                return [
                    'success' => true,
                    'message' => 'Statistics retrieved successfully',
                    'data' => $response['data'] ?? []
                ];
            }

            return [
                'success' => false,
                'message' => $response['message'] ?? 'Failed to load statistics',
                'data' => []
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to load statistics: ' . $e->getMessage(),
                'data' => []
            ];
        }
    }

    /**
     * Delete a disaster report (admin action)
     * 
     * @param int $reportId Report ID to delete
     * @return array Standardized response
     */
    public function deleteReport($reportId)
    {
        try {
            // Note: Using generic endpoint as specific Gibran delete endpoint may not exist
            $endpoint = $this->apiClient->getEndpoint('reports', 'destroy', ['id' => $reportId]);
            $response = $this->apiClient->authenticatedRequest('DELETE', $endpoint);

            if (isset($response['success']) && $response['success']) {
                return [
                    'success' => true,
                    'message' => 'Report deleted successfully'
                ];
            }

            return [
                'success' => false,
                'message' => $response['message'] ?? 'Failed to delete report'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to delete report: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get pending reports (those awaiting verification)
     * 
     * @return array Pending reports
     */
    public function getPendingReports()
    {
        return $this->getAllReports(['status' => 'pending']);
    }

    /**
     * Get user's reports (for verification display)
     * 
     * @param int|null $userId Optional user ID filter
     * @return array User reports
     */
    public function getUserReports($userId = null)
    {
        $filters = [];
        if ($userId) {
            $filters['user_id'] = $userId;
        }

        return $this->getAllReports($filters);
    }

    /**
     * Get a single report by ID
     * 
     * @param int $reportId Report ID
     * @return array Single report data
     */
    public function getReport($reportId)
    {
        try {
            $endpoint = $this->apiClient->getEndpoint('reports', 'show', ['id' => $reportId]);
            $response = $this->apiClient->authenticatedRequest('GET', $endpoint);

            if (isset($response['success']) && $response['success']) {
                return [
                    'success' => true,
                    'message' => 'Report retrieved successfully',
                    'data' => $response['data'] ?? []
                ];
            }

            return [
                'success' => false,
                'message' => $response['message'] ?? 'Report not found',
                'data' => []
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to retrieve report: ' . $e->getMessage(),
                'data' => []
            ];
        }
    }

    /**
     * Update an existing report
     * 
     * @param int $reportId Report ID
     * @param array $reportData Updated report data
     * @param array $files Optional file uploads
     * @return array Standardized response
     */
    public function updateReport($reportId, $reportData, $files = [])
    {
        try {
            $endpoint = $this->apiClient->getEndpoint('reports', 'update', ['id' => $reportId]);

            // Use the existing authenticatedRequest method which supports files
            $response = $this->apiClient->authenticatedRequest('PUT', $endpoint, $reportData, $files);

            if (isset($response['success']) && $response['success']) {
                return [
                    'success' => true,
                    'message' => 'Report updated successfully',
                    'data' => $response['data'] ?? []
                ];
            }

            return [
                'success' => false,
                'message' => $response['message'] ?? 'Failed to update report',
                'errors' => $response['errors'] ?? null
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to update report: ' . $e->getMessage()
            ];
        }
    }
}
