<?php

namespace App\Http\Controllers;

use App\Services\AstacalaApiClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class ProfileAdminController extends Controller
{
    protected $apiClient;

    public function __construct(AstacalaApiClient $apiClient)
    {
        $this->apiClient = $apiClient;
    }

    public function show(Request $request)
    {
        // Check if admin is logged in
        if (!session()->has('admin_id')) {
            return redirect('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        try {
            // Get admin profile from the unified backend API
            $endpoint = $this->apiClient->getEndpoint('users', 'profile');
            $response = $this->apiClient->authenticatedRequest('GET', $endpoint);

            if (isset($response['success']) && $response['success'] && isset($response['data'])) {
                // Convert API response to format expected by view
                $admin = (object) [
                    'username_akun_admin' => session('admin_username'),
                    'nama_lengkap_admin' => $response['data']['name'] ?? session('admin_name'),
                    'tanggal_lahir_admin' => $response['data']['birth_date'] ?? 'N/A',
                    'tempat_lahir_admin' => $response['data']['place_of_birth'] ?? 'N/A',
                    'no_handphone_admin' => $response['data']['phone'] ?? 'N/A',
                    'no_anggota' => $response['data']['member_number'] ?? 'N/A'
                ];

                return view('profil_admin', compact('admin'));
            }

            // If API fails, use session data as fallback
            $admin = (object) [
                'username_akun_admin' => session('admin_username'),
                'nama_lengkap_admin' => session('admin_name'),
                'tanggal_lahir_admin' => 'N/A',
                'tempat_lahir_admin' => 'N/A',
                'no_handphone_admin' => 'N/A',
                'no_anggota' => 'N/A'
            ];

            return view('profil_admin', compact('admin'));
        } catch (\Exception $e) {
            Log::error('Profile show error: ' . $e->getMessage());

            // Use session data as fallback
            $admin = (object) [
                'username_akun_admin' => session('admin_username'),
                'nama_lengkap_admin' => session('admin_name'),
                'tanggal_lahir_admin' => 'N/A',
                'tempat_lahir_admin' => 'N/A',
                'no_handphone_admin' => 'N/A',
                'no_anggota' => 'N/A'
            ];

            return view('profil_admin', compact('admin'));
        }
    }

    public function edit(Request $request)
    {
        // Check if admin is logged in
        if (!session()->has('admin_id')) {
            return redirect('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        try {
            // Get admin profile from the unified backend API
            $endpoint = $this->apiClient->getEndpoint('users', 'profile');
            $response = $this->apiClient->authenticatedRequest('GET', $endpoint);

            if (isset($response['success']) && $response['success'] && isset($response['data'])) {
                // Convert API response to format expected by view
                $admin = (object) [
                    'username_akun_admin' => session('admin_username'),
                    'nama_lengkap_admin' => $response['data']['name'] ?? session('admin_name'),
                    'tanggal_lahir_admin' => $response['data']['birth_date'] ?? '',
                    'tempat_lahir_admin' => $response['data']['place_of_birth'] ?? '',
                    'no_handphone_admin' => $response['data']['phone'] ?? '',
                    'no_anggota' => $response['data']['member_number'] ?? ''
                ];

                return view('edit_profil_admin', compact('admin'));
            }

            // If API fails, use session data as fallback
            $admin = (object) [
                'username_akun_admin' => session('admin_username'),
                'nama_lengkap_admin' => session('admin_name'),
                'tanggal_lahir_admin' => '',
                'tempat_lahir_admin' => '',
                'no_handphone_admin' => '',
                'no_anggota' => ''
            ];

            return view('edit_profil_admin', compact('admin'));
        } catch (\Exception $e) {
            Log::error('Profile edit error: ' . $e->getMessage());

            // Use session data as fallback
            $admin = (object) [
                'username_akun_admin' => session('admin_username'),
                'nama_lengkap_admin' => session('admin_name'),
                'tanggal_lahir_admin' => '',
                'tempat_lahir_admin' => '',
                'no_handphone_admin' => '',
                'no_anggota' => ''
            ];

            return view('edit_profil_admin', compact('admin'));
        }
    }

    public function update(Request $request)
    {
        // Check if admin is logged in
        if (!session()->has('admin_id')) {
            return redirect('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $request->validate([
            'username_akun_admin' => 'required',
            'nama_lengkap_admin' => 'required',
            'tanggal_lahir_admin' => 'required|date',
            'tempat_lahir_admin' => 'required',
            'no_anggota' => 'required',
            'no_handphone_admin' => 'required',
        ]);

        try {
            // Update admin profile via the unified backend API
            $endpoint = $this->apiClient->getEndpoint('users', 'update_profile');
            $updateData = [
                'name' => $request->nama_lengkap_admin,
                'birth_date' => $request->tanggal_lahir_admin,
                'place_of_birth' => $request->tempat_lahir_admin,
                'phone' => $request->no_handphone_admin,
                'member_number' => $request->no_anggota
            ];

            $response = $this->apiClient->authenticatedRequest('PUT', $endpoint, $updateData);

            if (isset($response['success']) && $response['success']) {
                // Update session data with new information
                session([
                    'admin_name' => $request->nama_lengkap_admin,
                    'admin_username' => $request->username_akun_admin
                ]);

                return redirect()->route('profil.admin')->with('success', 'Profil berhasil diperbarui.');
            }

            Log::warning('Profile update failed', ['response' => $response]);
            return redirect()->back()->with('error', 'Gagal memperbarui profil. Silakan coba lagi.');
        } catch (\Exception $e) {
            Log::error('Profile update error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem.');
        }
    }
}
