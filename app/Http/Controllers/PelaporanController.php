<?php

namespace App\Http\Controllers;

use App\Services\GibranAuthService;
use App\Services\GibranReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PelaporanController extends Controller
{
    protected $reportService;

    protected $authService;

    public function __construct(GibranReportService $reportService, GibranAuthService $authService)
    {
        $this->reportService = $reportService;
        $this->authService = $authService;
    }

    public function membacaDataPelaporan()
    {
        try {
            $result = $this->reportService->getAllReports();
            if ($result['success'] === true) {
                return view('data_pelaporan', ['data' => $result['data']]);
            } else {
                Log::error('Failed to fetch reports: '.$result['message']);

                return view('data_pelaporan', ['data' => []]);
            }
        } catch (\Exception $e) {
            Log::error('Exception in membacaDataPelaporan: '.$e->getMessage());

            return view('data_pelaporan', ['data' => []]);
        }
    }

    public function menghapusDataPelaporan($id)
    {
        try {
            $result = $this->reportService->deleteReport($id);
            if ($result['success'] === true) {
                return redirect()->route('pelaporan.index')->with('success', 'Laporan berhasil dihapus');
            } else {
                return redirect()->route('pelaporan.index')->with('error', 'Gagal menghapus laporan: '.$result['message']);
            }
        } catch (\Exception $e) {
            Log::error('Exception in menghapusDataPelaporan: '.$e->getMessage());

            return redirect()->route('pelaporan.index')->with('error', 'Terjadi kesalahan saat menghapus laporan');
        }
    }

    public function menampilkanNotifikasiPelaporanMasuk()
    {
        try {
            $result = $this->reportService->getAllReports();
            if ($result['success'] === true) {
                return view('notifikasi', ['data' => $result['data']]);
            } else {
                Log::error('Failed to fetch reports for notifications: '.$result['message']);

                return view('notifikasi', ['data' => []]);
            }
        } catch (\Exception $e) {
            Log::error('Exception in menampilkanNotifikasiPelaporanMasuk: '.$e->getMessage());

            return view('notifikasi', ['data' => []]);
        }
    }

    public function memberikanNotifikasiVerifikasi($id)
    {
        try {
            $result = $this->reportService->verifyReport($id);
            if ($result['success'] === true) {
                return redirect()->route('pelaporan.notifikasi')->with('success', 'Laporan berhasil diverifikasi');
            } else {
                return redirect()->route('pelaporan.notifikasi')->with('error', 'Gagal memverifikasi laporan: '.$result['message']);
            }
        } catch (\Exception $e) {
            Log::error('Exception in memberikanNotifikasiVerifikasi: '.$e->getMessage());

            return redirect()->route('pelaporan.notifikasi')->with('error', 'Terjadi kesalahan saat memverifikasi laporan');
        }
    }

    public function getVerifikasi($pengguna_id)
    {
        try {
            $result = $this->reportService->getReportsByUser($pengguna_id);

            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('Exception in getVerifikasi: '.$e->getMessage());

            return response()->json(['status' => 'error', 'message' => 'Internal server error'], 500);
        }
    }

    public function apiDeleteReport($id)
    {
        try {
            $result = $this->reportService->deleteReport($id);

            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('Exception in apiDeleteReport: '.$e->getMessage());

            return response()->json(['status' => 'error', 'message' => 'Internal server error'], 500);
        }
    }

    public function apiVerifyReport($id)
    {
        try {
            $result = $this->reportService->verifyReport($id);

            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('Exception in apiVerifyReport: '.$e->getMessage());

            return response()->json(['status' => 'error', 'message' => 'Internal server error'], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $result = $this->reportService->createReport($request->all());

            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('Exception in store: '.$e->getMessage());

            return response()->json(['status' => 'error', 'message' => 'Internal server error'], 500);
        }
    }

    // TICKET #003: Navigation and Detail Views
    public function showDetail($id)
    {
        try {
            $result = $this->reportService->getReport($id);

            if ($result['success'] === true) {
                return view('detail_pelaporan', ['report' => $result['data']]);
            } else {
                Log::error('Failed to fetch report detail: '.$result['message']);

                return redirect()->route('pelaporan.index')
                    ->with('error', 'Report not found: '.$result['message']);
            }
        } catch (\Exception $e) {
            Log::error('Exception in showDetail: '.$e->getMessage());

            return redirect()->route('pelaporan.index')
                ->with('error', 'Error loading report detail');
        }
    }

    public function showNotifikasiDetail($id)
    {
        try {
            $result = $this->reportService->getReport($id);

            if ($result['success'] === true) {
                return view('detail_notifikasi', ['notifikasi' => $result['data']]);
            } else {
                Log::error('Failed to fetch notifikasi detail: '.$result['message']);

                return redirect()->route('pelaporan.notifikasi')
                    ->with('error', 'Notifikasi not found: '.$result['message']);
            }
        } catch (\Exception $e) {
            Log::error('Exception in showNotifikasiDetail: '.$e->getMessage());

            return redirect()->route('pelaporan.notifikasi')
                ->with('error', 'Error loading notifikasi detail');
        }
    }
}
