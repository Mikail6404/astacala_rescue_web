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
        <div style="margin-left: 100px; margin-top:50px;">
            <h1 style="font-family: Arial, Helvetica, sans-serif; color:#FF8E27; font-size: x-large;">Ubah Pengguna</h1>
        </div>
        <section class="width-100% height-auto" style="margin-left: 80px; display: flex;">
            <div style="margin-top: 40px;">
                <?php
                    // Handle both array and object data structures
                    $id = is_array($pengguna) ? ($pengguna['id'] ?? 0) : ($pengguna->id ?? 0);
                    $username = is_array($pengguna) ? ($pengguna['username'] ?? $pengguna['email'] ?? '') : ($pengguna->username_akun_pengguna ?? $pengguna->username ?? $pengguna->email ?? '');
                    $name = is_array($pengguna) ? ($pengguna['name'] ?? '') : ($pengguna->nama_lengkap_pengguna ?? $pengguna->name ?? '');
                    $birthDate = is_array($pengguna) ? ($pengguna['date_of_birth'] ?? '') : ($pengguna->tanggal_lahir_pengguna ?? $pengguna->date_of_birth ?? '');
                    $birthPlace = is_array($pengguna) ? ($pengguna['place_of_birth'] ?? '') : ($pengguna->tempat_lahir_pengguna ?? $pengguna->place_of_birth ?? '');
                    $phone = is_array($pengguna) ? ($pengguna['phone'] ?? '') : ($pengguna->no_handphone_pengguna ?? $pengguna->phone ?? '');
                    $password = is_array($pengguna) ? '****' : ($pengguna->password_akun_pengguna ?? '****');
                ?>
                <form action="/Pengguna/<?php echo e($id); ?>" method="POST">
                    <?php echo method_field('put'); ?>
                    <?php echo csrf_field(); ?>
                    <div style="margin-bottom: 20px">
                        <input type="text" name="username_akun_pengguna" placeholder="username_akun_pengguna"
                            value="<?php echo e($username); ?>" style="width: 400px; height: 40px"><br>
                    </div>
                    <div style="margin-bottom: 20px">
                        <input type="text" name="nama_lengkap_pengguna" placeholder="nama_lengkap_pengguna"
                            value="<?php echo e($name); ?>" style="width: 400px; height: 40px"> <br>
                    </div>
                    <div style="margin-bottom: 20px">
                        <input type="date" name="tanggal_lahir_pengguna" placeholder="tanggal_lahir_pengguna"
                            value="<?php echo e($birthDate); ?>" style="width: 400px; height: 40px">
                        <br>
                    </div>
                    <div style="margin-bottom: 20px">
                        <input type="text" name="tempat_lahir_pengguna" placeholder="tempat_lahir_pengguna"
                            value="<?php echo e($birthPlace); ?>" style="width: 400px; height: 40px"> <br>
                    </div>
                    <div style="margin-bottom: 20px">
                        <input type="text" name="no_handphone_pengguna" placeholder="no_handphone_pengguna"
                            value="<?php echo e($phone); ?>"style="width: 400px; height: 40px"> <br>
                    </div>
                    <div style="margin-bottom: 20px">
                        <input type="text" name="password_akun_pengguna" placeholder="password_akun_pengguna"
                            value="<?php echo e($password); ?>"style="width: 400px; height: 40px"> <br>
                    </div>
                    <div style="margin-bottom: 20px">
                        <input type="submit" name="submit" value="update"
                            style="width: 100px; height: 30px; background-color:#FF8E27; border-radius:5px">
                    </div>
                </form>
            </div>
        </section>
    </div>
</body>

</html>
<?php /**PATH D:\astacala_rescue_mobile\astacala_resque-main\astacala_rescue_web\resources\views/ubah_pengguna.blade.php ENDPATH**/ ?>