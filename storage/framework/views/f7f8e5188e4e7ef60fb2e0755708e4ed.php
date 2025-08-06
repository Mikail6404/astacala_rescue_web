<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sidebar Navigation</title>
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
        <form action="<?php echo e(route('berita.update', $data->id)); ?>" method="POST" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>
            <div>
                <label>Judul Bencana:</label>
                <input type="text" name="pblk_judul_bencana" value="<?php echo e($data->pblk_judul_bencana); ?>" required>
            </div>

            <div>
                <label>Lokasi Bencana:</label>
                <input type="text" name="pblk_lokasi_bencana" value="<?php echo e($data->pblk_lokasi_bencana); ?>" required>
            </div>

            <div>
                <label>Titik Koordinat:</label>
                <input type="text" name="pblk_titik_kordinat_bencana"
                    value="<?php echo e($data->pblk_titik_kordinat_bencana); ?>" required>
            </div>

            <div>
                <label>Skala Bencana:</label>
                <select name="pblk_skala_bencana" required>
                    <option value="kecil" <?php echo e($data->pblk_skala_bencana == 'kecil' ? 'selected' : ''); ?>>Kecil</option>
                    <option value="sedang" <?php echo e($data->pblk_skala_bencana == 'sedang' ? 'selected' : ''); ?>>Sedang</option>
                    <option value="besar" <?php echo e($data->pblk_skala_bencana == 'besar' ? 'selected' : ''); ?>>Besar</option>
                </select>
            </div>

            <div>
                <label>Deskripsi Umum:</label>
                <textarea name="deskripsi_umum" required><?php echo e($data->deskripsi_umum); ?></textarea>
            </div>

            <div>
                <label>Dibuat Oleh Admin ID:</label>
                <input type="number" name="dibuat_oleh_admin_id" value="<?php echo e($data->dibuat_oleh_admin_id); ?>" required>
            </div>

            <div>
                <label>Foto Bencana:</label>
                <?php if($data->pblk_foto_bencana): ?>
                    <img src="<?php echo e(asset('storage/' . $data->pblk_foto_bencana)); ?>" width="100">
                <?php endif; ?>
                <input type="file" name="pblk_foto_bencana">
            </div>

            <button type="submit">Update</button>
        </form>
    </div>
</body>

</html>
<?php /**PATH D:\astacala_rescue_mobile\astacala_resque-main\astacala_rescue_web\resources\views/ubah_data_publikasi.blade.php ENDPATH**/ ?>