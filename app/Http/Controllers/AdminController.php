<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GibranUserService;

class AdminController extends Controller
{
    protected $userService;

    public function __construct(GibranUserService $userService)
    {
        $this->userService = $userService;
    }

    public function controlleradmin()
    {
        // Check if admin is logged in
        if (!session()->has('admin_id')) {
            return redirect('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // Get admins using dedicated admin endpoint
        $response = $this->userService->getAdminUsers();

        if ($response['success']) {
            $data_admin = $response['data'];
            return view('data_admin', compact('data_admin'));
        } else {
            // If API call fails, show empty state with error message
            $data_admin = [];
            return view('data_admin', compact('data_admin'))
                ->with('error', 'Gagal memuat data admin: ' . $response['message']);
        }
    }

    public function hapusadmin($id)
    {
        $response = $this->userService->deleteUser($id);

        if ($response['success']) {
            return redirect('/Dataadmin')->with('Success', 'Data Admin Berhasil Dihapus');
        } else {
            return redirect('/Dataadmin')->with('Error', $response['message']);
        }
    }

    /**
     * Delete admin user via AJAX API call
     * For TICKET #001: CRUD Operations
     */
    public function apiDeleteAdmin($id)
    {
        try {
            $response = $this->userService->deleteUser($id);

            if ($response['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'Data admin berhasil dihapus'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $response['message'] ?? 'Gagal menghapus data admin'
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update admin user via AJAX API call
     * For TICKET #005: CRUD Operations - Fix Issue 5a
     */
    public function apiUpdateAdmin($id, Request $request)
    {
        try {
            // Validate the request data (removed username_akun_admin - not updatable for security)
            $validated = $request->validate([
                'nama_lengkap_admin' => 'sometimes|string|max:255',
                'tanggal_lahir_admin' => 'sometimes|date',
                'tempat_lahir_admin' => 'sometimes|string|max:255',
                'no_handphone_admin' => 'sometimes|string|max:50',
                'no_anggota' => 'sometimes|string|max:50',
                'password_akun_admin' => 'sometimes|string|min:6'
            ]);

            // Remove empty fields to avoid overwriting with blanks
            $adminData = array_filter($validated, function ($value) {
                return !empty($value);
            });

            $response = $this->userService->updateUser($id, $adminData);

            if ($response['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'Data admin berhasil diperbarui',
                    'data' => $response['data'] ?? null
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $response['message'] ?? 'Gagal memperbarui data admin'
            ], 400);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ], 500);
        }
    }

    public function ubahadmin($id)
    {
        $response = $this->userService->getUser($id);

        if ($response['success']) {
            $admin = $response['data'];
            return view('ubah_admin', compact(['admin']));
        } else {
            return redirect('/Dataadmin')->with('Error', $response['message']);
        }
    }

    public function ubahadmi($id, Request $request)
    {
        $adminData = $request->except(['_token', 'submit']);
        $response = $this->userService->updateUser($id, $adminData);

        if ($response['success']) {
            return redirect('/Dataadmin')->with('Success', 'Data Admin Berhasil Diperbarui');
        } else {
            return redirect('/Dataadmin')->with('Error', $response['message']);
        }
    }
}
