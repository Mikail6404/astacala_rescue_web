<?php

namespace App\Services;

use Exception;

/**
 * Gibran Notification Service
 *
 * Handles notification operations for the web application using
 * the /api/gibran/notifikasi/* endpoints from the unified backend.
 *
 * This service provides web admin notification functionality for
 * managing and sending notifications to mobile users.
 *
 * @author Web Integration Team
 *
 * @version 1.0.0
 *
 * @date August 3, 2025
 */
class GibranNotificationService
{
    protected $apiClient;

    public function __construct(AstacalaApiClient $apiClient)
    {
        $this->apiClient = $apiClient;
    }

    /**
     * Send notification to specific user
     *
     * @param  array  $notificationData  Notification content and recipient info
     * @return array Standardized response
     */
    public function sendNotification($notificationData)
    {
        try {
            $endpoint = $this->apiClient->getEndpoint('gibran', 'notifikasi_send');
            $response = $this->apiClient->authenticatedRequest('POST', $endpoint, $notificationData);

            if (isset($response['success']) && $response['success']) {
                return [
                    'success' => true,
                    'message' => 'Notification sent successfully',
                    'data' => $response['data'] ?? [],
                ];
            }

            return [
                'success' => false,
                'message' => $response['message'] ?? 'Failed to send notification',
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to send notification: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Get notifications for specific user
     *
     * @param  int  $penggunaId  User ID
     * @return array User notifications
     */
    public function getUserNotifications($penggunaId)
    {
        try {
            $endpoint = $this->apiClient->getEndpoint('gibran', 'notifikasi_user', ['pengguna_id' => $penggunaId]);
            $response = $this->apiClient->authenticatedRequest('GET', $endpoint);

            if (isset($response['success']) && $response['success']) {
                return [
                    'success' => true,
                    'message' => 'User notifications retrieved successfully',
                    'data' => $response['data'] ?? [],
                ];
            }

            return [
                'success' => false,
                'message' => $response['message'] ?? 'Failed to load user notifications',
                'data' => [],
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to load user notifications: '.$e->getMessage(),
                'data' => [],
            ];
        }
    }

    /**
     * Send verification notification for a report
     *
     * @param  int  $reportId  Report ID
     * @param  string  $status  Verification status (verified, rejected, etc.)
     * @param  string  $message  Optional message for the user
     * @return array Response
     */
    public function sendVerificationNotification($reportId, $status, $message = '')
    {
        $notificationData = [
            'type' => 'report_verification',
            'report_id' => $reportId,
            'status' => $status,
            'message' => $message ?: "Your disaster report has been {$status}",
            'priority' => 'high',
        ];

        return $this->sendNotification($notificationData);
    }

    /**
     * Send broadcast notification to all users
     *
     * @param  array  $broadcastData  Broadcast message content
     * @return array Response
     */
    public function sendBroadcastNotification($broadcastData)
    {
        try {
            // Use general notification broadcast endpoint
            $endpoint = $this->apiClient->getEndpoint('notifications', 'broadcast');
            $response = $this->apiClient->authenticatedRequest('POST', $endpoint, $broadcastData);

            if (isset($response['success']) && $response['success']) {
                return [
                    'success' => true,
                    'message' => 'Broadcast notification sent successfully',
                    'data' => $response['data'] ?? [],
                ];
            }

            return [
                'success' => false,
                'message' => $response['message'] ?? 'Failed to send broadcast notification',
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to send broadcast notification: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Send emergency alert notification
     *
     * @param  array  $emergencyData  Emergency alert content
     * @return array Response
     */
    public function sendEmergencyAlert($emergencyData)
    {
        $alertData = array_merge($emergencyData, [
            'type' => 'emergency_alert',
            'priority' => 'critical',
            'immediate' => true,
        ]);

        return $this->sendBroadcastNotification($alertData);
    }
}
