<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
    @php
        // Get notification count from statistics data passed by controller
        $jumlah_notifikasi = isset($statistics['pending_notifications']) ? $statistics['pending_notifications'] : 0;
    @endphp

    <div class="flex h-screen">
        
        <!-- Sidebar -->
        <div class="w-48 bg-white text-black flex flex-col h-screen border-r shadow-md font-semibold"
            x-data="{ openPublikasi: false }">
            <!-- Logo & Profil -->
            <div class="p-4 text-center border-b">
                <img src="{{ asset('image/yayasan_astacala_logo.png') }}" alt="Logo Profil" class="w-16 h-16 mx-auto">
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
                        <a href="{{ route('logout') }}"
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

        <!-- Alpine.js CDN (untuk dropdown) -->
        <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>


        <!-- Main Content -->
        <div class="flex-grow p-6">
            <div class="flex justify-end mb-4">
                <!-- Tombol Notifikasi -->
                <a href="{{ route('pelaporan.notifikasi') }}"
                    class="relative inline-flex items-center px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                    <i class="fas fa-bell mr-2"></i> Notifikasi
                    @if ($jumlah_notifikasi > 0)
                        <span
                            class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-red-100 bg-yellow-500 rounded-full">
                            {{ $jumlah_notifikasi }}
                        </span>
                    @endif
                </a>
                <!-- Tombol Profil -->
                <a href="{{ route('profil.admin') }}"
                    class="inline-flex items-center px-8 py-2 ml-5 bg-gray-600 text-white rounded hover:bg-gray-700">
                    <i class="fas fa-user mr-2"></i> Profil
                </a>
            </div>

            <h1 class="text-3xl font-bold text-gray-800">Welcome to the dashboard admin :)</h1>

            <div class="grid grid-cols-3 gap-4 mt-14">
                <div class="bg-cyan-500 text-white rounded-lg p-6 shadow-xl">
                    <div class="flex items-center">
                        <div class="text-5xl">1</div>
                        <div class="ml-4">
                            <h2 class="text-lg">Data Pelaporan</h2>
                            <a href="/pelaporan" class="hover:underline">More Info ></a>
                        </div>
                    </div>
                </div>
                <div class="bg-red-500 text-white rounded-lg p-6 shadow-xl">
                    <div class="flex items-center">
                        <div class="text-5xl">3</div>
                        <div class="ml-4">
                            <h2 class="text-lg">Admin</h2>
                            <a href="/Dataadmin" class="hover:underline">More Info ></a>
                        </div>
                    </div>
                </div>
                <div class="bg-green-500 text-white rounded-lg p-6 shadow-xl">
                    <div class="flex items-center">
                        <div class="text-5xl">1</div>
                        <div class="ml-4">
                            <h2 class="text-lg">Pengguna</h2>
                            <a href="/Datapengguna" class="hover:underline">More Info ></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</body>

</html>
