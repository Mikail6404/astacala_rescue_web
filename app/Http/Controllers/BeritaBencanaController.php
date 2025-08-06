<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GibranContentService;

class BeritaBencanaController extends Controller
{
    protected $contentService;

    public function __construct(GibranContentService $contentService)
    {
        $this->contentService = $contentService;
    }

    // Tampilkan semua data
    public function membacaDataPublikasiBeritaBencana()
    {
        $response = $this->contentService->getAllPublications();

        if ($response['success']) {
            $data = $response['data'];
            return view('data_publikasi', compact('data'));
        } else {
            return view('data_publikasi', ['data' => []])
                ->with('error', $response['message']);
        }
    }

    // Tampilkan form tambah data
    public function tambahDataPublikasiBeritaBencana()
    {
        return view('tambah_data_publikasi');
    }

    // Simpan data baru
    public function simpanTambahPublikasiDataBeritaBencana(Request $request)
    {
        $validated = $request->validate([
            'pblk_judul_bencana' => 'required',
            'pblk_lokasi_bencana' => 'required',
            'pblk_titik_kordinat_bencana' => 'required',
            'pblk_skala_bencana' => 'required',
            'deskripsi_umum' => 'required',
            'pblk_foto_bencana' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'dibuat_oleh_admin_id' => 'required|integer',
        ]);

        // Prepare files for upload
        $files = [];
        if ($request->hasFile('pblk_foto_bencana')) {
            $files['disaster_image'] = $request->file('pblk_foto_bencana');
        }

        $response = $this->contentService->createPublication($validated, $files);

        if ($response['success']) {
            return redirect()->route('berita.index')->with('success', 'Data berhasil ditambahkan');
        } else {
            return redirect()->route('berita.index')->with('error', $response['message']);
        }
    }

    // Tampilkan form edit
    public function editDataPublikasiBeritaBencana($id)
    {
        $response = $this->contentService->getPublication($id);

        if ($response['success']) {
            $rawData = $response['data'];

            // Transform API data to match template expectations
            $data = new \stdClass();
            $data->id = $rawData['id'];

            // Parse tags JSON to extract disaster-specific data
            $tagsData = json_decode($rawData['tags'] ?? '{}', true);

            // Map standard publication fields to legacy template field names
            $data->pblk_judul_bencana = $rawData['title'];
            $data->pblk_lokasi_bencana = $tagsData['location'] ?? '';
            $data->pblk_titik_kordinat_bencana = $tagsData['coordinates'] ?? '';
            $data->pblk_skala_bencana = $tagsData['severity'] ?? '';
            $data->deskripsi_umum = $rawData['content'];
            $data->pblk_foto_bencana = $rawData['featured_image'] ?? '';
            $data->dibuat_oleh_admin_id = $rawData['author_id'];

            return view('ubah_data_publikasi', compact('data'));
        } else {
            return redirect()->route('berita.index')->with('error', $response['message']);
        }
    }

    // Simpan perubahan data
    public function simpanEditDataPublikasiBeritaBencana(Request $request, $id)
    {
        $validated = $request->validate([
            'pblk_judul_bencana' => 'required',
            'pblk_lokasi_bencana' => 'required',
            'pblk_titik_kordinat_bencana' => 'required',
            'pblk_skala_bencana' => 'required',
            'deskripsi_umum' => 'required',
            'pblk_foto_bencana' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'dibuat_oleh_admin_id' => 'required|integer',
        ]);

        // Prepare files for upload
        $files = [];
        if ($request->hasFile('pblk_foto_bencana')) {
            $files['disaster_image'] = $request->file('pblk_foto_bencana');
        }

        $response = $this->contentService->updatePublication($id, $validated, $files);

        if ($response['success']) {
            return redirect('/publikasi')->with('success', 'Data berhasil diperbarui');
        } else {
            return redirect('/publikasi')->with('error', $response['message']);
        }
    }

    public function hapusDataPublikasiBeritaBencana($id)
    {
        $response = $this->contentService->deletePublication($id);

        if ($response['success']) {
            return redirect()->route('berita.index')->with('success', 'Data berhasil dihapus.');
        } else {
            return redirect()->route('berita.index')->with('error', $response['message']);
        }
    }

    public function publishPublikasiBeritaBencana($id)
    {
        $response = $this->contentService->publishPublication($id);

        if ($response['success']) {
            return redirect()->route('berita.index')->with('success', 'Berita berhasil dipublish!');
        } else {
            return redirect()->route('berita.index')->with('error', $response['message']);
        }
    }

    public function apiPublishPublikasiBeritaBencana()
    {
        $response = $this->contentService->getPublishedContent();

        if ($response['success']) {
            return response()->json($response['data']);
        } else {
            return response()->json([
                'success' => false,
                'message' => $response['message']
            ], 400);
        }
    }

    public function getPublished()
    {
        $response = $this->contentService->getPublishedContent();

        if ($response['success']) {
            return response()->json($response['data']);
        } else {
            return response()->json([
                'success' => false,
                'message' => $response['message']
            ], 400);
        }
    }

    public function show($id)
    {
        $response = $this->contentService->getPublication($id);

        if ($response['success']) {
            return response()->json([
                'success' => true,
                'data' => $response['data']
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => $response['message']
            ], 404);
        }
    }
}
