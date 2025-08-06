<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Astacala Rescue Backend API Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for integrating with the Astacala Rescue backend API.
    | This allows the web application to communicate with the unified backend
    | system that serves both mobile and web platforms.
    |
    */

    'base_url' => env('API_BASE_URL', 'http://127.0.0.1:8000'),

    'version' => env('API_VERSION', 'v1'),

    'timeout' => env('API_TIMEOUT', 30),

    'retry_attempts' => env('API_RETRY_ATTEMPTS', 3),

    'endpoints' => [
        'auth' => [
            'login' => '/api/{version}/auth/login',
            'register' => '/api/{version}/auth/register',
            'logout' => '/api/{version}/auth/logout',
            'me' => '/api/{version}/auth/me',
            'refresh' => '/api/{version}/auth/refresh',
            'forgot_password' => '/api/{version}/auth/forgot-password',
            'reset_password' => '/api/{version}/auth/reset-password',
        ],

        'reports' => [
            'index' => '/api/{version}/reports',
            'store' => '/api/{version}/reports',
            'show' => '/api/{version}/reports/{id}',
            'update' => '/api/{version}/reports/{id}',
            'destroy' => '/api/{version}/reports/{id}',
            'statistics' => '/api/{version}/reports/statistics',
            'web_submit' => '/api/{version}/reports/web-submit',
            'admin_view' => '/api/{version}/reports/admin-view',
            'pending' => '/api/{version}/reports/pending',
            'verify' => '/api/{version}/reports/{id}/verify',
            'publish' => '/api/{version}/reports/{id}/publish',
            'my_reports' => '/api/{version}/reports/my-reports',
            'my_statistics' => '/api/{version}/reports/my-statistics',
        ],

        'users' => [
            'profile' => '/api/{version}/users/profile',
            'get_by_id' => '/api/{version}/users/{id}',
            'update_profile' => '/api/{version}/users/profile',
            'update_user_by_id' => '/api/{version}/users/{id}',
            'delete_user_by_id' => '/api/{version}/users/{id}',
            'upload_avatar' => '/api/{version}/users/profile/avatar',
            'reports' => '/api/{version}/users/reports',
            'admin_list' => '/api/{version}/users/admin-list',
            'volunteer_list' => '/api/{version}/users/volunteer-list',
            'create_admin' => '/api/{version}/users/create-admin',
            'update_role' => '/api/{version}/users/{id}/role',
            'update_status' => '/api/{version}/users/{id}/status',
            'statistics' => '/api/{version}/users/statistics',
        ],

        'notifications' => [
            'index' => '/api/{version}/notifications',
            'mark_read' => '/api/{version}/notifications/mark-read',
            'unread_count' => '/api/{version}/notifications/unread-count',
            'destroy' => '/api/{version}/notifications/{id}',
            'update_fcm_token' => '/api/{version}/notifications/fcm-token',
            'broadcast' => '/api/{version}/notifications/broadcast',
        ],

        'publications' => [
            'index' => '/api/{version}/publications',
            'show' => '/api/{version}/publications/{id}',
            'store' => '/api/{version}/publications',
            'update' => '/api/{version}/publications/{id}',
            'destroy' => '/api/{version}/publications/{id}',
            'publish' => '/api/{version}/publications/{id}/publish',
        ],

        'files' => [
            'upload_disaster_images' => '/api/{version}/files/disasters/{reportId}/images',
            'delete_image' => '/api/{version}/files/disasters/{reportId}/images/{imageId}',
            'upload_document' => '/api/{version}/files/disasters/{reportId}/documents',
            'upload_avatar' => '/api/{version}/files/avatar',
            'storage_statistics' => '/api/{version}/files/storage/statistics',
        ],

        // Gibran-specific endpoints for web application integration
        'gibran' => [
            'auth_login' => '/api/gibran/auth/login',
            'berita_bencana' => '/api/gibran/berita-bencana',
            'publications' => '/api/gibran/publications',
            'dashboard_statistics' => '/api/gibran/dashboard/statistics',
            'notifikasi_send' => '/api/gibran/notifikasi/send',
            'notifikasi_user' => '/api/gibran/notifikasi/{pengguna_id}',
            'pelaporans_list' => '/api/gibran/pelaporans',
            'pelaporans_create' => '/api/gibran/pelaporans',
            'pelaporans_show' => '/api/gibran/pelaporans/{id}',
            'pelaporans_destroy' => '/api/gibran/pelaporans/{id}',
            'pelaporans_verify' => '/api/gibran/pelaporans/{id}/verify',
        ],
    ],

    'jwt' => [
        'refresh_buffer' => env('JWT_REFRESH_BUFFER', 300), // seconds before expiry to refresh
        'storage_key' => 'astacala_jwt_token',
        'user_key' => 'astacala_user_data',
    ],

    'headers' => [
        'Accept' => 'application/json',
        'Content-Type' => 'application/json',
        'X-Requested-With' => 'XMLHttpRequest',
    ],
];
