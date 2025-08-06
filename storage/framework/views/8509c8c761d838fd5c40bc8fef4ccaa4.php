<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>Sidebar Navigation</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
</head>

<body class="bg-gray-100">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <!-- Sidebar -->
        <div class="w-48 bg-white text-black flex flex-col h-screen border-r shadow-md font-semibold"
            x-data="{ openPublikasi: false }">
            <!-- Logo & Profil -->
            <div class="p-4 text-center border-b">
                <img src="<?php echo e(asset('image/yayasan_astacala_logo.png')); ?>" alt="Logo Profil" class="w-16 h-16 mx-auto">
                <p class="mt-2 text-sm">Admin</p>
            </div>

            <!-- Nav -->
            <nav class="flex-grow">
                <ul class="mt-6 space-y-1">
                    <!-- Home -->
                    <li>
                        <a href="/Home" class="flex items-center px-4 py-3 hover:bg-gray-100 transition">
                            <i class="fas fa-home-alt mr-2"></i>
                            <span>Menu Utama</span>
                        </a>
                    </li>

                    <!-- Publikasi -->
                    <li>
                        <button @click="openPublikasi = !openPublikasi"
                            class="flex justify-between items-center w-full px-4 py-3 hover:bg-gray-100 transition">
                            <div class="flex items-center">
                                <i class="fas fa-database mr-2"></i>
                                <span>Publikasi</span>
                            </div>
                            <i :class="openPublikasi ? 'fas fa-chevron-up' : 'fas fa-chevron-down'"></i>
                        </button>

                        <!-- Dropdown -->
                        <ul x-show="openPublikasi" x-transition class="ml-8 mt-1 space-y-1">
                            <li>
                                <a href="/publikasi" class="block px-4 py-2 text-sm hover:bg-gray-100 rounded">
                                    - Publikasi Berita
                                </a>
                            </li>
                            <li>
                                <a href="#" class="block px-4 py-2 text-sm hover:bg-gray-100 rounded">
                                    - Forum Diskusi
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- Logout -->
                    <li>
                        <a href="<?php echo e(route('logout')); ?>"
                            class="flex items-center px-4 py-3 hover:bg-gray-100 transition">
                            <i class="fas fa-sign-out-alt mr-2"></i>
                            <span>Keluar</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <!-- Footer -->
            <div class="p-4 border-t text-center text-xs text-gray-500">
                &copy; 2025 Astacala Rescue
            </div>
        </div>

        <!-- Alpine.js -->
        <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>


        <!-- Main Content -->
        <div class="flex-grow p-6">
            <h1 class="text-4xl font-bold text-center font-sansz mt-5">Data Pelaporan</h1>

            <div class="overflow-x-auto rounded-lg shadow mt-16">
                <table class="min-w-full bg-white border border-gray-300 text-sm text-center">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-3 border">Username Pengguna</th>
                            <th class="px-4 py-3 border">Nama Tim</th>
                            <th class="px-4 py-3 border">Jumlah Personel</th>
                            <th class="px-4 py-3 border">No HP</th>
                            <th class="px-4 py-3 border">Informasi Singkat</th>
                            <th class="px-4 py-3 border">Lokasi</th>
                            <th class="px-4 py-3 border">Koordinat</th>
                            <th class="px-4 py-3 border">Skala</th>
                            <th class="px-4 py-3 border">Jumlah Korban</th>
                            <th class="px-4 py-3 border">Deskripsi</th>
                            <th class="px-4 py-3 border">Foto Lokasi</th>
                            <th class="px-4 py-3 border">Bukti Tugas</th>
                            <th class="px-4 py-3 border">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <!-- Data count: <?php echo e(count($data ?? [])); ?> -->
                        <?php $__empty_1 = true; $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <?php
                                // Handle both array and object data structures with enhanced backend field mapping
                                $username = is_array($row) ? ($row['reporter_username'] ?? $row['reporter']['username'] ?? $row['username'] ?? 'N/A') : ($row->reporter_username ?? $row->pengguna->username_akun_pengguna ?? $row->username ?? 'N/A');
                                $teamName = is_array($row) ? ($row['team_name'] ?? $row['nama_team_pelapor'] ?? 'N/A') : ($row->nama_team_pelapor ?? $row->team_name ?? 'N/A');
                                $personnel = is_array($row) ? ($row['personnel_count'] ?? $row['jumlah_personel'] ?? 0) : ($row->jumlah_personel ?? $row->personnel_count ?? 0);
                                $phone = is_array($row) ? ($row['reporter_phone'] ?? $row['contact_phone'] ?? $row['no_handphone'] ?? 'N/A') : ($row->reporter_phone ?? $row->no_handphone ?? $row->contact_phone ?? 'N/A');
                                $title = is_array($row) ? ($row['title'] ?? $row['informasi_singkat_bencana'] ?? 'N/A') : ($row->informasi_singkat_bencana ?? $row->title ?? 'N/A');
                                $location = is_array($row) ? ($row['location_name'] ?? $row['lokasi_bencana'] ?? 'N/A') : ($row->lokasi_bencana ?? $row->location_name ?? 'N/A');
                                $coordinates = is_array($row) ? ($row['coordinate_display'] ?? $row['coordinates'] ?? $row['titik_kordinat_lokasi_bencana'] ?? 'N/A') : ($row->coordinate_display ?? $row->titik_kordinat_lokasi_bencana ?? $row->coordinates ?? 'N/A');
                                $scale = is_array($row) ? ($row['severity_level'] ?? $row['skala_bencana'] ?? 'N/A') : ($row->skala_bencana ?? $row->severity_level ?? 'N/A');
                                $casualties = is_array($row) ? ($row['casualties'] ?? $row['jumlah_korban'] ?? 0) : ($row->jumlah_korban ?? $row->casualties ?? 0);
                                $description = is_array($row) ? ($row['description'] ?? $row['deskripsi_terkait_data_lainya'] ?? 'N/A') : ($row->deskripsi_terkait_data_lainya ?? $row->description ?? 'N/A');
                                $photo = is_array($row) ? ($row['images'][0]['url'] ?? $row['foto_lokasi_bencana'] ?? null) : ($row->foto_lokasi_bencana ?? null);
                                $document = is_array($row) ? ($row['evidence_document'] ?? $row['bukti_surat_perintah_tugas'] ?? null) : ($row->bukti_surat_perintah_tugas ?? null);
                                $id = is_array($row) ? ($row['id'] ?? 0) : ($row->id ?? 0);
                            ?>
                            <tr class="hover:bg-gray-100">
                                <td class="px-4 py-3"><?php echo e($username); ?></td>
                                <td class="px-4 py-3"><?php echo e($teamName); ?></td>
                                <td class="px-4 py-3"><?php echo e($personnel); ?></td>
                                <td class="px-4 py-3"><?php echo e($phone); ?></td>
                                <td class="px-4 py-3"><?php echo e($title); ?></td>
                                <td class="px-4 py-3"><?php echo e($location); ?></td>
                                <td class="px-4 py-3">
                                    <?php echo e($coordinates); ?>

                                    <button onclick="showMap('<?php echo e($coordinates); ?>')"
                                        class="ml-2 text-blue-600 hover:text-blue-800">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>
                                <td class="px-4 py-3"><?php echo e($scale); ?></td>
                                <td class="px-4 py-3"><?php echo e($casualties); ?></td>
                                <td class="px-4 py-3"><?php echo e($description); ?></td>
                                <td class="px-4 py-3">
                                    <?php if($photo): ?>
                                        <img src="<?php echo e(asset('storage/' . $photo)); ?>"
                                            alt="Foto Lokasi" class="w-24 h-auto rounded shadow">
                                    <?php else: ?>
                                        <span class="text-gray-500">Tidak ada foto</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3">
                                    <?php if($document): ?>
                                        <a href="<?php echo e(asset('storage/' . $document)); ?>"
                                            target="_blank" class="text-blue-600 underline hover:text-blue-800">Lihat
                                            File</a>
                                    <?php else: ?>
                                        <span class="text-gray-500">Tidak ada file</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3 space-x-2">
                                    <a href="<?php echo e(route('pelaporan.detail', ['id' => $id])); ?>" class="px-4 py-1 bg-blue-500 text-white rounded hover:bg-blue-600">Detail</a>
                                    <button type="button" onclick="deleteReport(<?php echo e($id); ?>)"
                                        class="px-6 py-1 bg-red-500 text-white rounded hover:bg-red-600">
                                        Delete
                                    </button>
                                    <button type="button" onclick="verifikasiData(<?php echo e($id); ?>)"
                                        class="px-5 py-1 mt-6 bg-green-500 text-white rounded hover:bg-green-600">
                                        Verifikasi
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="13" class="px-4 py-3 text-center text-gray-500">
                                    <?php if(isset($error)): ?>
                                        <?php echo e($error); ?>

                                    <?php else: ?>
                                        Tidak ada data pelaporan tersedia
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>



        </div>
    </div>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // TICKET #001: CRUD Operations - Delete Report Function
        function deleteReport(id) {
            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: "Apakah Anda yakin ingin menghapus data pelaporan ini?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading
                    Swal.fire({
                        title: 'Menghapus...',
                        text: 'Sedang memproses penghapusan data',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        willOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Make AJAX DELETE request
                    fetch(`/api/pelaporan/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '<?php echo e(csrf_token()); ?>'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: 'Berhasil!',
                                text: data.message,
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                // Reload page to refresh data
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: 'Gagal!',
                                text: data.message || 'Terjadi kesalahan saat menghapus data',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Delete error:', error);
                        Swal.fire({
                            title: 'Error!',
                            text: 'Terjadi kesalahan sistem. Silakan coba lagi.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    });
                }
            });
        }

        // TICKET #001: CRUD Operations - Verify Report Function  
        function verifikasiData(id) {
            Swal.fire({
                title: 'Verifikasi Pelaporan',
                input: 'textarea',
                inputLabel: 'Catatan (opsional)',
                inputPlaceholder: 'Masukkan catatan verifikasi...',
                showCancelButton: true,
                confirmButtonText: 'DITERIMA',
                cancelButtonText: 'DITOLAK',
                showDenyButton: true,
                denyButtonText: 'DITOLAK',
                customClass: {
                    actions: 'swal2-actions-custom',
                    confirmButton: 'swal2-confirm-green',
                    denyButton: 'swal2-deny-red'
                }
            }).then((result) => {
                if (result.isConfirmed || result.isDenied) {
                    const status = result.isConfirmed ? 'DITERIMA' : 'DITOLAK';
                    const notes = result.value || '';

                    // Show loading
                    Swal.fire({
                        title: 'Memproses...',
                        text: 'Sedang memperbarui status verifikasi',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        willOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Make AJAX POST request
                    fetch(`/api/pelaporan/${id}/verify`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '<?php echo e(csrf_token()); ?>'
                        },
                        body: JSON.stringify({
                            status: status,
                            notes: notes
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: 'Berhasil!',
                                text: `Status verifikasi berhasil diperbarui: ${data.status}`,
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                // Reload page to refresh data
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: 'Gagal!',
                                text: data.message || 'Terjadi kesalahan saat memperbarui status',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Verify error:', error);
                        Swal.fire({
                            title: 'Error!',
                            text: 'Terjadi kesalahan sistem. Silakan coba lagi.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    });
                }
            });
        }

        function showMap(kordinat) {
            if (!kordinat || !kordinat.includes(',')) {
                Swal.fire('Error', 'Titik koordinat bencana tidak tersedia', 'error');
                return;
            }

            const [lat, lng] = kordinat.split(',').map(x => parseFloat(x.trim()));

            Swal.fire({
                title: 'Lokasi Peta',
                html: '<div id="map" style="height:400px;"></div>',
                width: 600,
                didOpen: () => {
                    const map = L.map('map').setView([lat, lng], 13);

                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '© OpenStreetMap contributors'
                    }).addTo(map);

                    L.marker([lat, lng]).addTo(map)
                        .bindPopup('Lokasi Bencana').openPopup();
                }
            });
        }
    </script>
    <style>
        .swal2-actions-custom {
            display: flex !important;
            justify-content: space-between !important;
        }
        .swal2-confirm-green {
            background-color: #10b981 !important;
        }
        .swal2-deny-red {
            background-color: #ef4444 !important;
        }
    </style>
</body>


</html>
<?php /**PATH D:\astacala_rescue_mobile\astacala_resque-main\astacala_rescue_web\resources\views/data_pelaporan.blade.php ENDPATH**/ ?>