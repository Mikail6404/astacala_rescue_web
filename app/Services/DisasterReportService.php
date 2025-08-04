<?php

namespace App\Services;

use App\Services\AstacalaApiClient;
use Exception;

class DisasterReportService
{
    protected $apiClient;

    public function __construct(AstacalaApiClient $apiClient)
    {
        $this->apiClient = $apiClient;
    }

    /**
     * Get all disaster reports
     */
    public function getAllReports($params = [])
    {
        try {
            $endpoint = $this->apiClient->getEndpoint('reports', 'index');
            $response = $this->apiClient->authenticatedRequest('GET', $endpoint, $params);

            return [
                'success' => true,
                'data' => $response['data'] ?? [],
                'pagination' => $response['pagination'] ?? null
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'data' => []
            ];
        }
    }

    /**
     * Get single disaster report
     */
    public function getReport($id)
    {
        try {
            $endpoint = $this->apiClient->getEndpoint('reports', 'show', ['id' => $id]);
            $response = $this->apiClient->authenticatedRequest('GET', $endpoint);

            return [
                'success' => true,
                'data' => $response['data'] ?? null
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null
            ];
        }
    }

    /**
     * Create new disaster report
     */
    public function createReport($data, $files = [])
    {
        try {
            // Use standard store endpoint which works for both mobile and web
            $endpoint = $this->apiClient->getEndpoint('reports', 'store');
            $response = $this->apiClient->authenticatedRequest('POST', $endpoint, $data, $files);

            return [
                'success' => true,
                'data' => $response['data'] ?? null,
                'message' => $response['message'] ?? 'Report created successfully'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null
            ];
        }
    }

    /**
     * Update disaster report
     */
    public function updateReport($id, $data, $files = [])
    {
        try {
            $endpoint = $this->apiClient->getEndpoint('reports', 'update', ['id' => $id]);
            $response = $this->apiClient->authenticatedRequest('PUT', $endpoint, $data, $files);

            return [
                'success' => true,
                'data' => $response['data'] ?? null,
                'message' => $response['message'] ?? 'Report updated successfully'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null
            ];
        }
    }

    /**
     * Delete disaster report
     */
    public function deleteReport($id)
    {
        try {
            $endpoint = $this->apiClient->getEndpoint('reports', 'destroy', ['id' => $id]);
            $response = $this->apiClient->authenticatedRequest('DELETE', $endpoint);

            return [
                'success' => true,
                'message' => $response['message'] ?? 'Report deleted successfully'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Get disaster reports statistics
     */
    public function getStatistics()
    {
        try {
            $endpoint = $this->apiClient->getEndpoint('reports', 'statistics');
            $response = $this->apiClient->authenticatedRequest('GET', $endpoint);

            return [
                'success' => true,
                'data' => $response['data'] ?? []
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'data' => []
            ];
        }
    }

    /**
     * Get pending reports (admin view)
     */
    public function getPendingReports()
    {
        try {
            $endpoint = $this->apiClient->getEndpoint('reports', 'pending');
            $response = $this->apiClient->authenticatedRequest('GET', $endpoint);

            return [
                'success' => true,
                'data' => $response['data'] ?? []
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'data' => []
            ];
        }
    }

    /**
     * Verify disaster report
     */
    public function verifyReport($id, $verificationData = [])
    {
        try {
            $endpoint = $this->apiClient->getEndpoint('reports', 'verify', ['id' => $id]);
            $response = $this->apiClient->authenticatedRequest('POST', $endpoint, $verificationData);

            return [
                'success' => true,
                'data' => $response['data'] ?? null,
                'message' => $response['message'] ?? 'Report verified successfully'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null
            ];
        }
    }

    /**
     * Publish disaster report
     */
    public function publishReport($id, $publishData = [])
    {
        try {
            $endpoint = $this->apiClient->getEndpoint('reports', 'publish', ['id' => $id]);
            $response = $this->apiClient->authenticatedRequest('POST', $endpoint, $publishData);

            return [
                'success' => true,
                'data' => $response['data'] ?? null,
                'message' => $response['message'] ?? 'Report published successfully'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null
            ];
        }
    }

    /**
     * Get user's reports
     */
    public function getUserReports()
    {
        try {
            $endpoint = $this->apiClient->getEndpoint('reports', 'my_reports');
            $response = $this->apiClient->authenticatedRequest('GET', $endpoint);

            return [
                'success' => true,
                'data' => $response['data'] ?? []
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'data' => []
            ];
        }
    }

    /**
     * Upload images for disaster report
     */
    public function uploadImages($reportId, $images)
    {
        try {
            $endpoint = $this->apiClient->getEndpoint('files', 'upload_disaster_images', ['reportId' => $reportId]);
            $response = $this->apiClient->authenticatedRequest('POST', $endpoint, [], $images);

            return [
                'success' => true,
                'data' => $response['data'] ?? null,
                'message' => $response['message'] ?? 'Images uploaded successfully'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null
            ];
        }
    }

    /**
     * Upload document for disaster report
     */
    public function uploadDocument($reportId, $document)
    {
        try {
            $endpoint = $this->apiClient->getEndpoint('files', 'upload_document', ['reportId' => $reportId]);
            $response = $this->apiClient->authenticatedRequest('POST', $endpoint, [], ['document' => $document]);

            return [
                'success' => true,
                'data' => $response['data'] ?? null,
                'message' => $response['message'] ?? 'Document uploaded successfully'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null
            ];
        }
    }
}
