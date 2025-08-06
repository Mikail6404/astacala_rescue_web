<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>Data Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
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
            <h1 class="text-4xl font-bold text-center font-sansz mt-5">Data Admin</h1>
            <div class="overflow-x-auto mt-16">
                <table class="min-w-full bg-white border border-gray-300">
                    <thead class="bg-gray-200 text-gray-600">
                        <tr>
                            <th class="border px-4 py-2">Username Admin</th>
                            <th class="border px-4 py-2">Nama Lengkap</th>
                            <th class="border px-4 py-2">Tanggal Lahir</th>
                            <th class="border px-4 py-2">Tempat Lahir</th>
                            <th class="border px-4 py-2">No Handphone</th>
                            <th class="border px-4 py-2">No Anggota</th>
                            <th class="border px-4 py-2">Password Admin</th>
                            <th class="border px-4 py-2">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $data_admin; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $admi): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <?php
                                // Handle both array and object data structures
                                $username = is_array($admi) ? ($admi['username'] ?? $admi['email'] ?? 'N/A') : ($admi->username_akun_admin ?? $admi->username ?? $admi->email ?? 'N/A');
                                $name = is_array($admi) ? ($admi['name'] ?? 'N/A') : ($admi->nama_lengkap_admin ?? $admi->name ?? 'N/A');
                                $birthDate = is_array($admi) ? ($admi['birth_date'] ?? 'N/A') : ($admi->tanggal_lahir_admin ?? $admi->birth_date ?? 'N/A');
                                $birthPlace = is_array($admi) ? ($admi['place_of_birth'] ?? $admi['birth_place'] ?? 'N/A') : ($admi->tempat_lahir_admin ?? $admi->place_of_birth ?? $admi->birth_place ?? 'N/A');
                                $phone = is_array($admi) ? ($admi['phone'] ?? 'N/A') : ($admi->no_handphone_admin ?? $admi->phone ?? 'N/A');
                                $memberNumber = is_array($admi) ? ($admi['member_number'] ?? $admi['organization'] ?? 'N/A') : ($admi->no_anggota ?? $admi->member_number ?? $admi->organization ?? 'N/A');
                                $password = is_array($admi) ? '****' : ($admi->password_akun_admin ?? '****');
                                $id = is_array($admi) ? ($admi['id'] ?? 0) : ($admi->id ?? 0);
                            ?>
                            <tr class="hover:bg-gray-100">
                                <td class="border px-4 py-2"><?php echo e($username); ?></td>
                                <td class="border px-4 py-2"><?php echo e($name); ?></td>
                                <td class="border px-4 py-2"><?php echo e($birthDate); ?></td>
                                <td class="border px-4 py-2"><?php echo e($birthPlace); ?></td>
                                <td class="border px-4 py-2"><?php echo e($phone); ?></td>
                                <td class="border px-4 py-2"><?php echo e($memberNumber); ?></td>
                                <td class="border px-4 py-2"><?php echo e($password); ?></td>
                                <td class="border px-4 py-2 text-center">
                                    <div class="flex flex-col items-center space-y-2">
                                        <a href="/Admin/<?php echo e($id); ?>/ubahadmin" onclick="return confirmUpdate()"
                                            class="px-4 py-2 mb-2 bg-yellow-500 text-white rounded hover:bg-yellow-600 inline-block">
                                            Update
                                        </a>
                                        <button type="button" onclick="deleteAdmin(<?php echo e($id); ?>)"
                                            class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600 inline-block">
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="8" class="border px-4 py-2 text-center text-gray-500">
                                    <?php if(isset($error)): ?>
                                        <?php echo e($error); ?>

                                    <?php else: ?>
                                        Tidak ada data admin tersedia
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
        function confirmUpdate() {
            return confirm("Apakah Anda yakin ingin mengedit data admin ini?");
        }

        function confirmDelete() {
            return confirm("Apakah Anda yakin ingin menghapus data admin ini?");
        }

        // TICKET #001: CRUD Operations - Delete Admin Function
        function deleteAdmin(id) {
            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: "Apakah Anda yakin ingin menghapus data admin ini?",
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
                        text: 'Sedang memproses penghapusan data admin',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        willOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Make AJAX DELETE request
                    fetch(`/api/admin/${id}`, {
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
                        console.error('Delete admin error:', error);
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
    </script>
</body>

</html>
<?php /**PATH D:\astacala_rescue_mobile\astacala_resque-main\astacala_rescue_web\resources\views/data_admin.blade.php ENDPATH**/ ?>