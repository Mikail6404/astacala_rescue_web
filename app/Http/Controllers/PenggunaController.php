<?php

// app/Http/Controllers/PenggunaController.php

namespace App\Http\Controllers;

use App\Services\GibranUserService;
use Illuminate\Http\Request;

class PenggunaController extends Controller
{
    protected $userService;

    public function __construct(GibranUserService $userService)
    {
        $this->userService = $userService;
    }

    public function controllerpengguna()
    {
        // Check if admin is logged in
        if (!session()->has('admin_id')) {
            return redirect('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // Get volunteers only (using dedicated volunteer endpoint)
        $response = $this->userService->getVolunteerUsers();

        if ($response['success']) {
            $data_pengguna = $response['data'];
            return view('data_pengguna', compact('data_pengguna'));
        } else {
            // If API call fails, show empty state with error message
            $data_pengguna = [];
            return view('data_pengguna', compact('data_pengguna'))
                ->with('error', 'Gagal memuat data pengguna: ' . $response['message']);
        }
    }

    public function hapus($id)
    {
        $response = $this->userService->deleteUser($id);

        if ($response['success']) {
            return redirect('/Datapengguna')->with('Success', 'Data Pengguna Berhasil Dihapus');
        } else {
            return redirect('/Datapengguna')->with('Error', $response['message']);
        }
    }

    public function ubahpenggun($id)
    {
        $response = $this->userService->getUser($id);

        if ($response['success']) {
            $pengguna = $response['data'];
            return view('ubah_pengguna', compact(['pengguna']));
        } else {
            return redirect('/Datapengguna')->with('Error', $response['message']);
        }
    }

    public function ubahpeng($id, Request $request)
    {
        $userData = $request->except(['_token', 'submit']);
        $response = $this->userService->updateUser($id, $userData);

        if ($response['success']) {
            return redirect('/Datapengguna')->with('Success', 'Data Pengguna Berhasil Diperbarui');
        } else {
            return redirect('/Datapengguna')->with('Error', $response['message']);
        }
    }
}
