<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\GibranAuthService;

class AuthAdminController extends Controller
{
    protected $gibranAuthService;

    public function __construct(GibranAuthService $gibranAuthService)
    {
        $this->gibranAuthService = $gibranAuthService;
    }

    /**
     * Map username from web form to email for unified backend authentication
     * 
     * Supports both legacy username format and direct email input
     */
    private function mapUsernameToEmail($username)
    {
        // If it's already an email, use it directly
        if (filter_var($username, FILTER_VALIDATE_EMAIL)) {
            return $username;
        }

        // Environment-based username mapping (configurable via .env)
        $defaultAdminEmail = env('DEFAULT_ADMIN_EMAIL', 'admin@uat.test');
        $defaultVolunteerEmail = env('DEFAULT_VOLUNTEER_EMAIL', 'volunteer@mobile.test');

        // Standard role-based mapping for common usernames
        $usernameToEmailMap = [
            'admin' => $defaultAdminEmail,
            'administrator' => $defaultAdminEmail,
            'volunteer' => $defaultVolunteerEmail,
            'test' => $defaultVolunteerEmail,
        ];

        // Return mapped email if username exists in map
        if (isset($usernameToEmailMap[strtolower($username)])) {
            return $usernameToEmailMap[strtolower($username)];
        }

        // For any other username, append the admin domain (since this is admin login)
        // This allows users to login with just their username instead of full email
        return $username . '@admin.astacala.local';
    }

    /**
     * Process credentials for backend authentication
     * 
     * Handles both direct credentials and legacy test credential mapping
     */
    private function processCredentialsForAuth($username, $password)
    {
        $email = $this->mapUsernameToEmail($username);

        // Environment-based test credential mapping (for UAT/demo purposes)
        $enableTestMapping = env('ENABLE_TEST_CREDENTIAL_MAPPING', true);

        if ($enableTestMapping) {
            // Allow common test passwords to map to environment-configured credentials
            $testPasswords = ['admin', 'password', 'test', '123456'];

            if (in_array(strtolower($password), $testPasswords)) {
                // Map to environment-specific credentials based on email domain
                if (str_contains($email, 'admin') || str_contains($email, 'uat.test')) {
                    return [
                        'email' => $email,
                        'password' => env('UAT_ADMIN_PASSWORD', 'admin123')
                    ];
                } else {
                    return [
                        'email' => $email,
                        'password' => env('UAT_VOLUNTEER_PASSWORD', 'password123')
                    ];
                }
            }
        }

        // Use credentials as provided (for direct email/password login)
        return ['email' => $email, 'password' => $password];
    }

    public function showLoginForm()
    {
        return view('login');
    }

    public function processLogin(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        try {
            // PRIMARY: Authenticate against unified backend API
            $mappedCredentials = $this->processCredentialsForAuth($credentials['username'], $credentials['password']);

            $authResult = $this->gibranAuthService->login($mappedCredentials);

            if ($authResult['success']) {
                // Authentication successful - tokens already stored by GibranAuthService
                // Store minimal session data for web app compatibility
                session([
                    'admin_id' => $authResult['user']['id'],
                    'admin_username' => $credentials['username'], // Keep original username for display
                    'admin_name' => $authResult['user']['name'],
                    'admin_email' => $authResult['user']['email'],
                    'authenticated' => true
                ]);

                Log::info('Unified backend authentication successful', [
                    'username' => $credentials['username'],
                    'email' => $mappedCredentials['email'],
                    'user_id' => $authResult['user']['id']
                ]);

                return redirect()->route('dashboard')->with('success', 'Login berhasil!');
            }

            // Log authentication failure
            Log::warning('Unified backend authentication failed', [
                'username' => $credentials['username'],
                'email' => $mappedCredentials['email'],
                'error' => $authResult['error'] ?? 'Unknown error'
            ]);

            return redirect()->back()
                ->withErrors(['credentials' => 'Username atau password salah. Pastikan Anda menggunakan akun yang terdaftar di sistem.'])
                ->withInput($request->only('username'));
        } catch (\Exception $e) {
            Log::error('Login error: ' . $e->getMessage());
            return redirect()->back()
                ->withErrors(['error' => 'Terjadi kesalahan sistem'])
                ->withInput($request->only('username'));
        }
    }

    public function showRegisterForm()
    {
        return view('register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'username_akun_admin' => 'required|string',
            'password_akun_admin' => 'required|confirmed',
            'nama_lengkap_admin' => 'required|string',
            'tanggal_lahir_admin' => 'required|date',
            'tempat_lahir_admin' => 'required|string',
            'no_anggota' => 'required|string',
            'no_handphone_admin' => 'required|string',
        ]);

        try {
            // Register user in unified backend system
            $registrationData = [
                'name' => $request->nama_lengkap_admin,
                'email' => $request->username_akun_admin . '@admin.astacala.local', // Convert username to email format
                'password' => $request->password_akun_admin,
                'password_confirmation' => $request->password_akun_admin_confirmation,
                'phone' => $request->no_handphone_admin,
                'role' => 'ADMIN',
                'organization' => 'Astacala Rescue Team',
                'birth_date' => $request->tanggal_lahir_admin,
            ];

            // Use API client to register user
            $result = $this->gibranAuthService->register($registrationData);

            if ($result['success']) {
                Log::info('Admin registration successful via unified backend', [
                    'username' => $request->username_akun_admin,
                    'name' => $request->nama_lengkap_admin,
                    'user_id' => $result['user']['id'] ?? null
                ]);

                return redirect('/login')->with('success', 'Registrasi berhasil! Silakan login dengan akun Anda.');
            } else {
                Log::warning('Admin registration failed via unified backend', [
                    'username' => $request->username_akun_admin,
                    'error' => $result['error'] ?? 'Unknown error'
                ]);

                return back()
                    ->withErrors(['error' => 'Registrasi gagal: ' . ($result['error'] ?? 'Terjadi kesalahan sistem')])
                    ->withInput();
            }
        } catch (\Exception $e) {
            Log::error('Admin registration error', [
                'username' => $request->username_akun_admin,
                'error' => $e->getMessage()
            ]);

            return back()
                ->withErrors(['error' => 'Terjadi kesalahan sistem. Silakan coba lagi.'])
                ->withInput();
        }
    }

    public function logout(Request $request)
    {
        // Clear local session
        session()->flush();

        // Optional: Also logout from API
        try {
            $this->gibranAuthService->logout();
        } catch (\Exception $e) {
            Log::warning('API logout failed', ['error' => $e->getMessage()]);
        }

        return redirect('/login')->with('success', 'Logout berhasil.');
    }
}
