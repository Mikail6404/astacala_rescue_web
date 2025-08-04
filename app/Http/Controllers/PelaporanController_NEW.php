<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GibranReportService;
use App\Services\GibranAuthService;

class PelaporanController extends Controller
{
    protected $reportService;
    protected $authService;

    public function __construct(GibranReportService $reportService, GibranAuthService $authService)
    {
        $this->reportService = $reportService;
        $this->authService = $authService;
    }

    // Tampilkan semua data pelaporan
    public function membacaDataPelaporan()
    {
        $result = $this->reportService->getAllReports();

        if ($result['success']) {
            $data = $result['data'];
            return view('data_pelaporan', compact('data'));
        }

        return view('data_pelaporan', ['data' => []])
            ->with('error', 'Failed to load disaster reports: ' . $result['message']);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_team_pelapor' => 'required',
            'jumlah_personel' => 'required|integer',
            'no_handphone' => 'required',
            'informasi_singkat_bencana' => 'required',
            'lokasi_bencana' => 'required',
            'titik_kordinat_lokasi_bencana' => 'required',
            'skala_bencana' => 'required',
            'jumlah_korban' => 'required|integer',
            'deskripsi_terkait_data_lainya' => 'required',
            'foto_lokasi_bencana' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'bukti_surat_perintah_tugas' => 'required|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        // Map form fields to API format
        $reportData = [
            'title' => $validated['informasi_singkat_bencana'],
            'description' => $validated['deskripsi_terkait_data_lainya'],
            'location' => $validated['lokasi_bencana'],
            'coordinates' => $validated['titik_kordinat_lokasi_bencana'],
            'disaster_type' => 'general', // You may want to add this field to the form
            'severity' => $validated['skala_bencana'],
            'affected_population' => $validated['jumlah_korban'],
            'team_name' => $validated['nama_team_pelapor'],
            'team_size' => $validated['jumlah_personel'],
            'contact_phone' => $validated['no_handphone'],
            'status' => 'reported'
        ];

        // Prepare files for upload
        $files = [];
        if ($request->hasFile('foto_lokasi_bencana')) {
            $files['disaster_image'] = $request->file('foto_lokasi_bencana');
        }
        if ($request->hasFile('bukti_surat_perintah_tugas')) {
            $files['task_document'] = $request->file('bukti_surat_perintah_tugas');
        }

        $result = $this->reportService->createReport($reportData, $files);

        if ($result['success']) {
            return response()->json(['message' => 'Pelaporan berhasil disimpan!'], 200);
        }

        return response()->json(['error' => $result['message']], 400);
    }

    // Tampilkan halaman notifikasi
    public function menampilkanNotifikasiPelaporanMasuk()
    {
        $result = $this->reportService->getPendingReports();

        if ($result['success']) {
            $data = $result['data'];
            return view('notifikasi', compact('data'));
        }

        return view('notifikasi', ['data' => []])
            ->with('error', 'Failed to load notifications: ' . $result['message']);
    }

    // Hapus data
    public function menghapusDataPelaporan($id)
    {
        $result = $this->reportService->deleteReport($id);

        if ($result['success']) {
            return redirect()->route('pelaporan.index')->with('success', 'Data berhasil dihapus');
        }

        return redirect()->route('pelaporan.index')->with('error', $result['message']);
    }

    public function memberikanNotifikasiVerifikasi(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:DITERIMA,DITOLAK',
        ]);

        $verificationData = [
            'status' => $request->status === 'DITERIMA' ? 'verified' : 'rejected',
            'verification_notes' => $request->notes ?? null
        ];

        $result = $this->reportService->verifyReport($id, $verificationData);

        if ($result['success']) {
            return redirect()->route('pelaporan.index')->with('success', 'Status verifikasi diperbarui.');
        }

        return redirect()->route('pelaporan.index')->with('error', $result['message']);
    }

    public function getVerifikasi($pengguna_id)
    {
        // Get user's reports through API
        $result = $this->reportService->getUserReports();

        if ($result['success']) {
            $data = collect($result['data'])
                ->filter(function ($report) {
                    return in_array($report['status'], ['verified', 'rejected']);
                })
                ->map(function ($report) {
                    return [
                        'id' => $report['id'],
                        'informasi_singkat_bencana' => $report['title'],
                        'status_verifikasi' => $report['status'] === 'verified' ? 'DITERIMA' : 'DITOLAK'
                    ];
                })
                ->values()
                ->all();

            return response()->json($data);
        }

        return response()->json([]);
    }

    /**
     * Show single disaster report
     */
    public function show($id)
    {
        $result = $this->reportService->getReport($id);

        if ($result['success']) {
            $report = $result['data'];
            return view('detail_pelaporan', compact('report'));
        }

        return redirect()->route('pelaporan.index')
            ->with('error', 'Report not found: ' . $result['message']);
    }

    /**
     * Show edit form
     */
    public function edit($id)
    {
        $result = $this->reportService->getReport($id);

        if ($result['success']) {
            $report = $result['data'];
            return view('edit_data_pelaporan', compact('report'));
        }

        return redirect()->route('pelaporan.index')
            ->with('error', 'Report not found: ' . $result['message']);
    }

    /**
     * Update disaster report
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nama_team_pelapor' => 'required',
            'jumlah_personel' => 'required|integer',
            'no_handphone' => 'required',
            'informasi_singkat_bencana' => 'required',
            'lokasi_bencana' => 'required',
            'titik_kordinat_lokasi_bencana' => 'required',
            'skala_bencana' => 'required',
            'jumlah_korban' => 'required|integer',
            'deskripsi_terkait_data_lainya' => 'required',
            'foto_lokasi_bencana' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'bukti_surat_perintah_tugas' => 'nullable|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        // Map form fields to API format
        $reportData = [
            'title' => $validated['informasi_singkat_bencana'],
            'description' => $validated['deskripsi_terkait_data_lainya'],
            'location' => $validated['lokasi_bencana'],
            'coordinates' => $validated['titik_kordinat_lokasi_bencana'],
            'severity' => $validated['skala_bencana'],
            'affected_population' => $validated['jumlah_korban'],
            'team_name' => $validated['nama_team_pelapor'],
            'team_size' => $validated['jumlah_personel'],
            'contact_phone' => $validated['no_handphone'],
        ];

        // Prepare files for upload
        $files = [];
        if ($request->hasFile('foto_lokasi_bencana')) {
            $files['disaster_image'] = $request->file('foto_lokasi_bencana');
        }
        if ($request->hasFile('bukti_surat_perintah_tugas')) {
            $files['task_document'] = $request->file('bukti_surat_perintah_tugas');
        }

        $result = $this->reportService->updateReport($id, $reportData, $files);

        if ($result['success']) {
            return redirect()->route('pelaporan.show', $id)
                ->with('success', 'Report updated successfully');
        }

        return back()->with('error', $result['message']);
    }
}
