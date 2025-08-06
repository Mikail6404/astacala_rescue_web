<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Data Pengguna</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet" />
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
            <h1 class="text-4xl font-bold text-center font-sansz mt-5">Data Pengguna</h1>
            <div class="overflow-x-auto mt-16">
                <table class="min-w-full bg-white border border-gray-300">
                    <!-- Tabel Header -->
                    <thead class="bg-gray-200 text-gray-600">
                        <tr>
                            <th class="px-4 py-2 border">Username Pengguna</th>
                            <th class="px-4 py-2 border">Nama Lengkap</th>
                            <th class="px-4 py-2 border">Tanggal Lahir</th>
                            <th class="px-4 py-2 border">Tempat Lahir</th>
                            <th class="px-4 py-2 border">No Handphone</th>
                            <th class="px-4 py-2 border">Password Pengguna</th>
                            <th class="px-4 py-2 border">Aksi</th>
                        </tr>
                    </thead>

                    <!-- Tabel Body -->
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $data_pengguna; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $penggun): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <?php
                                // Handle both array and object data structures
                                $username = is_array($penggun) ? ($penggun['username'] ?? $penggun['email'] ?? 'N/A') : ($penggun->username_akun_pengguna ?? $penggun->username ?? $penggun->email ?? 'N/A');
                                $name = is_array($penggun) ? ($penggun['name'] ?? 'N/A') : ($penggun->nama_lengkap_pengguna ?? $penggun->name ?? 'N/A');
                                $birthDate = is_array($penggun) ? ($penggun['birth_date'] ?? 'N/A') : ($penggun->tanggal_lahir_pengguna ?? $penggun->birth_date ?? 'N/A');
                                $birthPlace = is_array($penggun) ? ($penggun['place_of_birth'] ?? $penggun['birth_place'] ?? 'N/A') : ($penggun->tempat_lahir_pengguna ?? $penggun->place_of_birth ?? $penggun->birth_place ?? 'N/A');
                                $phone = is_array($penggun) ? ($penggun['phone'] ?? 'N/A') : ($penggun->no_handphone_pengguna ?? $penggun->phone ?? 'N/A');
                                $password = is_array($penggun) ? '****' : ($penggun->password_akun_pengguna ?? '****');
                                $id = is_array($penggun) ? ($penggun['id'] ?? 0) : ($penggun->id ?? 0);
                            ?>
                            <tr class="hover:bg-gray-100">
                                <td class="px-4 py-2 border"><?php echo e($username); ?></td>
                                <td class="px-4 py-2 border"><?php echo e($name); ?></td>
                                <td class="px-4 py-2 border"><?php echo e($birthDate); ?></td>
                                <td class="px-4 py-2 border"><?php echo e($birthPlace); ?></td>
                                <td class="px-4 py-2 border"><?php echo e($phone); ?></td>
                                <td class="px-4 py-2 border"><?php echo e($password); ?></td>
                                <td class="px-4 py-2 border text-center flex flex-col items-center space-y-2">
                                    <a href="/Pengguna/<?php echo e($id); ?>/ubahpenggun" onclick="return confirmUpdate()"
                                        class="px-4 py-2 mb-2 bg-yellow-500 text-white rounded hover:bg-yellow-600 inline-block">
                                        Update
                                    </a>
                                    <form action="<?php echo e(url('/hapus_pengguna/' . $id)); ?>" method="POST"
                                        class="inline">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" onclick="return confirmDelete()"
                                            class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600 inline-block">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="7" class="px-4 py-8 border text-center text-gray-500">
                                    <div class="flex flex-col items-center space-y-2">
                                        <i class="fas fa-users text-4xl text-gray-300"></i>
                                        <p>Tidak ada data pengguna yang tersedia</p>
                                        <p class="text-sm">Data akan muncul setelah ada pengguna yang terdaftar</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        function confirmUpdate() {
            return confirm("Apakah Anda yakin ingin mengedit data pengguna ini?");
        }

        function confirmDelete() {
            return confirm("Apakah Anda yakin ingin menghapus data pengguna ini?");
        }
    </script>
</body>

</html>
<?php /**PATH D:\astacala_rescue_mobile\astacala_resque-main\astacala_rescue_web\resources\views/data_pengguna.blade.php ENDPATH**/ ?>