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
            $endpoint = $this->apiClient->getEndpoint('gibran', 'berita_bencana');
            $response = $this->apiClient->authenticatedRequest('GET', $endpoint, $filters);

            if (isset($response['success']) && $response['success']) {
                return [
                    'success' => true,
                    'message' => 'Publications retrieved successfully',
                    'data' => $response['data'] ?? []
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
            $endpoint = $this->apiClient->getEndpoint('gibran', 'berita_bencana');
            $response = $this->apiClient->authenticatedRequest('GET', $endpoint, ['id' => $publicationId]);

            if (isset($response['success']) && $response['success']) {
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
            // Map web form data to backend API format
            $apiData = [
                'title' => $publicationData['pblk_judul_bencana'] ?? $publicationData['title'],
                'content' => $publicationData['deskripsi_umum'] ?? $publicationData['content'],
                'type' => 'disaster_news',
                'category' => 'disaster_alert',
                'location' => $publicationData['pblk_lokasi_bencana'] ?? null,
                'coordinates' => $publicationData['pblk_titik_kordinat_bencana'] ?? null,
                'severity' => $publicationData['pblk_skala_bencana'] ?? null,
                'status' => 'draft', // Default to draft
                'author_id' => $publicationData['dibuat_oleh_admin_id'] ?? null
            ];

            $endpoint = $this->apiClient->getEndpoint('gibran', 'berita_bencana');
            $response = $this->apiClient->authenticatedRequest('POST', $endpoint, $apiData, $files);

            if (isset($response['success']) && $response['success']) {
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
            // Map web form data to backend API format
            $apiData = [
                'title' => $publicationData['pblk_judul_bencana'] ?? $publicationData['title'],
                'content' => $publicationData['deskripsi_umum'] ?? $publicationData['content'],
                'location' => $publicationData['pblk_lokasi_bencana'] ?? null,
                'coordinates' => $publicationData['pblk_titik_kordinat_bencana'] ?? null,
                'severity' => $publicationData['pblk_skala_bencana'] ?? null
            ];

            $endpoint = $this->apiClient->getEndpoint('gibran', 'berita_bencana');
            $response = $this->apiClient->authenticatedRequest('PUT', $endpoint, array_merge($apiData, ['id' => $publicationId]), $files);

            if (isset($response['success']) && $response['success']) {
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
            $endpoint = $this->apiClient->getEndpoint('gibran', 'berita_bencana');
            $response = $this->apiClient->authenticatedRequest('DELETE', $endpoint, ['id' => $publicationId]);

            if (isset($response['success']) && $response['success']) {
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
            $endpoint = $this->apiClient->getEndpoint('gibran', 'berita_bencana');
            $response = $this->apiClient->authenticatedRequest('PUT', $endpoint, [
                'id' => $publicationId,
                'status' => 'published',
                'published_at' => now()->toISOString()
            ]);

            if (isset($response['success']) && $response['success']) {
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
