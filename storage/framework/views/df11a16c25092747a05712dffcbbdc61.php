<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pelaporan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
    <div class="flex h-screen">
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
                                <a href="/pengguna" class="block px-4 py-2 text-sm hover:bg-gray-100 rounded">
                                    - Data Pengguna
                                </a>
                            </li>
                            <li>
                                <a href="/admin" class="block px-4 py-2 text-sm hover:bg-gray-100 rounded">
                                    - Data Admin
                                </a>
                            </li>
                            <li>
                                <a href="/pelaporan" class="block px-4 py-2 text-sm hover:bg-gray-100 rounded">
                                    - Data Pelaporan
                                </a>
                            </li>
                            <li>
                                <a href="/notifikasi" class="block px-4 py-2 text-sm hover:bg-gray-100 rounded">
                                    - Notifikasi
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- Keluar -->
                    <li>
                        <a href="<?php echo e(url('logout')); ?>" class="flex items-center px-4 py-3 hover:bg-gray-100 transition">
                            <i class="fas fa-sign-out-alt mr-2"></i>
                            <span>Keluar</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <!-- Footer -->
            <div class="p-4 text-xs text-center border-t">
                © 2025 Astacala Rescue
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 p-8 overflow-y-auto">
            <h1 class="text-2xl font-bold text-blue-700 mb-6">Detail Pelaporan</h1>
            <?php if(isset($report)): ?>
                <div class="bg-white shadow rounded p-6">
                    <p class="mb-3"><strong>Judul:</strong> <?php echo e($report['title'] ?? '-'); ?></p>
                    <p class="mb-3"><strong>Lokasi:</strong> <?php echo e($report['location_name'] ?? '-'); ?></p>
                    <p class="mb-3"><strong>Koordinat:</strong> <?php echo e($report['coordinates'] ?? '-'); ?></p>
                    <p class="mb-3"><strong>Jenis Bencana:</strong> <?php echo e($report['disaster_type'] ?? '-'); ?></p>
                    <p class="mb-3"><strong>Tingkat Keparahan:</strong> <?php echo e($report['severity_level'] ?? '-'); ?></p>
                    <p class="mb-3"><strong>Deskripsi:</strong> <?php echo e($report['description'] ?? '-'); ?></p>
                    <p class="mb-3"><strong>Dibuat Oleh:</strong> <?php echo e($report['team_name'] ?? '-'); ?></p>
                    <p class="mb-3"><strong>Status:</strong> <?php echo e($report['status'] ?? '-'); ?></p>
                    <p class="mb-3"><strong>Tanggal:</strong> <?php echo e($report['created_at'] ?? '-'); ?></p>
                    
                    <?php if(isset($report['images']) && count($report['images']) > 0): ?>
                        <div class="mt-4">
                            <strong>Gambar:</strong>
                            <div class="grid grid-cols-3 gap-4 mt-2">
                                <?php $__currentLoopData = $report['images']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $image): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <img src="<?php echo e($image['url']); ?>" alt="Gambar Laporan" class="w-full h-32 object-cover rounded border">
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                <a href="<?php echo e(route('pelaporan.index')); ?>" class="mt-6 inline-block bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded">Kembali ke Pelaporan</a>
            <?php else: ?>
                <p>Data pelaporan tidak ditemukan.</p>
                <a href="<?php echo e(route('pelaporan.index')); ?>" class="mt-6 inline-block bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded">Kembali ke Pelaporan</a>
            <?php endif; ?>
        </div>
    </div>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</body>

</html>
<?php /**PATH D:\astacala_rescue_mobile\astacala_resque-main\astacala_rescue_web\resources\views/detail_pelaporan.blade.php ENDPATH**/ ?>