<?php

namespace App\Services;

use App\Services\AstacalaApiClient;
use Exception;

/**
 * Gibran Content Service
 * 
 * Handles content/publication management operations for the web application using
 * the unified backend API instead of local database queries.
 * 
 * This service provides web admin functionality for managing disaster news,
 * publications, and other content across both mobile and web platforms.
 * 
 * @author Web Integration Team
 * @version 1.0.0
 * @date August 3, 2025
 */
class GibranContentService
{
    protected $apiClient;

    public function __construct(AstacalaApiClient $apiClient)
    {
        $this->apiClient = $apiClient;
    }

    /**
     * Get all publications/content for admin management
     * 
     * @param array $filters Optional filters (type, category, status, etc.)
     * @return array Standardized response with publications data
     */
    public function getAllPublications($filters = [])
    {
        try {
            // Use standard publications endpoint instead of Gibran-specific one
            $endpoint = $this->apiClient->getEndpoint('publications', 'index');
            $response = $this->apiClient->authenticatedRequest('GET', $endpoint, $filters);

            if (isset($response['status']) && $response['status'] === 'success') {
                // Handle paginated response - extract the actual data array
                $publicationsData = $response['data']['data'] ?? $response['data'] ?? [];

                return [
                    'success' => true,
                    'message' => $response['message'] ?? 'Publications retrieved successfully',
                    'data' => $publicationsData
                ];
            }

            return [
                'success' => false,
                'message' => $response['message'] ?? 'Failed to load publications',
                'data' => []
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to load publications: ' . $e->getMessage(),
                'data' => []
            ];
        }
    }

    /**
     * Get a specific publication by ID
     * 
     * @param int $publicationId Publication ID
     * @return array Standardized response with publication data
     */
    public function getPublication($publicationId)
    {
        try {
            // Use standard publications endpoint with ID parameter
            $endpoint = $this->apiClient->getEndpoint('publications', 'show', ['id' => $publicationId]);
            $response = $this->apiClient->authenticatedRequest('GET', $endpoint);

            if (isset($response['status']) && $response['status'] === 'success') {
                return [
                    'success' => true,
                    'message' => 'Publication retrieved successfully',
                    'data' => $response['data'] ?? null
                ];
            }

            return [
                'success' => false,
                'message' => $response['message'] ?? 'Publication not found'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to load publication: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Create a new publication
     * 
     * @param array $publicationData Publication data
     * @param array $files Optional file uploads
     * @return array Standardized response
     */
    public function createPublication($publicationData, $files = [])
    {
        try {
            // Store disaster-specific data in tags as JSON
            $disasterInfo = [
                'location' => $publicationData['pblk_lokasi_bencana'] ?? null,
                'coordinates' => $publicationData['pblk_titik_kordinat_bencana'] ?? null,
                'severity' => $publicationData['pblk_skala_bencana'] ?? null
            ];

            // Map web form data to backend API format
            $apiData = [
                'title' => $publicationData['pblk_judul_bencana'] ?? $publicationData['title'],
                'content' => $publicationData['deskripsi_umum'] ?? $publicationData['content'],
                'type' => 'report_summary', // Valid type for disaster reports
                'category' => 'disaster_alert',
                'tags' => json_encode($disasterInfo), // Store disaster-specific info in tags
                'status' => 'published', // Publish immediately for disaster reports
                'meta_description' => 'Disaster report - ' . ($publicationData['pblk_lokasi_bencana'] ?? 'Unknown location')
            ];

            // Use standard publications store endpoint
            $endpoint = $this->apiClient->getEndpoint('publications', 'store');
            $response = $this->apiClient->authenticatedRequest('POST', $endpoint, $apiData, $files);

            if (isset($response['status']) && $response['status'] === 'success') {
                return [
                    'success' => true,
                    'message' => 'Publication created successfully',
                    'data' => $response['data'] ?? []
                ];
            }

            return [
                'success' => false,
                'message' => $response['message'] ?? 'Failed to create publication',
                'errors' => $response['errors'] ?? null
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to create publication: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Update a publication
     * 
     * @param int $publicationId Publication ID to update
     * @param array $publicationData Updated publication data
     * @param array $files Optional file uploads
     * @return array Standardized response
     */
    public function updatePublication($publicationId, $publicationData, $files = [])
    {
        try {
            // Store disaster-specific data in tags as JSON
            $disasterInfo = [
                'location' => $publicationData['pblk_lokasi_bencana'] ?? null,
                'coordinates' => $publicationData['pblk_titik_kordinat_bencana'] ?? null,
                'severity' => $publicationData['pblk_skala_bencana'] ?? null
            ];

            // Map web form data to backend API format
            $apiData = [
                'title' => $publicationData['pblk_judul_bencana'] ?? $publicationData['title'],
                'content' => $publicationData['deskripsi_umum'] ?? $publicationData['content'],
                'type' => 'report_summary', // Valid type for disaster reports
                'category' => 'disaster_alert',
                'tags' => json_encode($disasterInfo), // Store disaster-specific info in tags
                'status' => 'published', // Keep published status for updates
                'meta_description' => 'Disaster report - ' . ($publicationData['pblk_lokasi_bencana'] ?? 'Unknown location')
            ];

            // Use standard publications update endpoint with ID parameter
            $endpoint = $this->apiClient->getEndpoint('publications', 'update', ['id' => $publicationId]);
            $response = $this->apiClient->authenticatedRequest('PUT', $endpoint, $apiData, $files);

            if (isset($response['status']) && $response['status'] === 'success') {
                return [
                    'success' => true,
                    'message' => 'Publication updated successfully',
                    'data' => $response['data'] ?? []
                ];
            }

            return [
                'success' => false,
                'message' => $response['message'] ?? 'Failed to update publication'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to update publication: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Delete a publication
     * 
     * @param int $publicationId Publication ID to delete
     * @return array Standardized response
     */
    public function deletePublication($publicationId)
    {
        try {
            // Use standard publications destroy endpoint with ID parameter
            $endpoint = $this->apiClient->getEndpoint('publications', 'destroy', ['id' => $publicationId]);
            $response = $this->apiClient->authenticatedRequest('DELETE', $endpoint);

            if (isset($response['status']) && $response['status'] === 'success') {
                return [
                    'success' => true,
                    'message' => 'Publication deleted successfully'
                ];
            }

            return [
                'success' => false,
                'message' => $response['message'] ?? 'Failed to delete publication'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to delete publication: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Publish a publication (change status to published)
     * 
     * @param int $publicationId Publication ID to publish
     * @return array Standardized response
     */
    public function publishPublication($publicationId)
    {
        try {
            // Use standard publications publish endpoint with ID parameter
            $endpoint = $this->apiClient->getEndpoint('publications', 'publish', ['id' => $publicationId]);
            $response = $this->apiClient->authenticatedRequest('POST', $endpoint, [
                'status' => 'published',
                'published_at' => now()->toISOString()
            ]);

            if (isset($response['status']) && $response['status'] === 'success') {
                return [
                    'success' => true,
                    'message' => 'Publication published successfully',
                    'data' => $response['data'] ?? []
                ];
            }

            return [
                'success' => false,
                'message' => $response['message'] ?? 'Failed to publish publication'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to publish publication: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get published content for public display
     * 
     * @param array $filters Optional filters
     * @return array Standardized response with published content
     */
    public function getPublishedContent($filters = [])
    {
        try {
            $filters['status'] = 'published';
            $endpoint = $this->apiClient->getEndpoint('gibran', 'berita_bencana');
            $response = $this->apiClient->authenticatedRequest('GET', $endpoint, $filters);

            if (isset($response['success']) && $response['success']) {
                return [
                    'success' => true,
                    'message' => 'Published content retrieved successfully',
                    'data' => $response['data'] ?? []
                ];
            }

            return [
                'success' => false,
                'message' => $response['message'] ?? 'Failed to load published content',
                'data' => []
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to load published content: ' . $e->getMessage(),
                'data' => []
            ];
        }
    }

    /**
     * Search publications by various criteria
     * 
     * @param array $searchCriteria Search parameters
     * @return array Standardized response with search results
     */
    public function searchPublications($searchCriteria)
    {
        try {
            $endpoint = $this->apiClient->getEndpoint('gibran', 'berita_bencana');
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
