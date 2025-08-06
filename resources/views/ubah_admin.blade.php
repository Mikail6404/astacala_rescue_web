<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Ubah Data Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100 flex h-screen">

    <!-- Sidebar -->
    <!-- Sidebar -->
    <div class="w-48 bg-white text-black flex flex-col h-screen border-r shadow-md font-semibold" x-data="{ openPublikasi: false }">
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
                    <a href="{{ route('logout') }}" class="flex items-center px-4 py-3 hover:bg-gray-100 transition">
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
    <div class="flex flex-grow items-center justify-center">
        <div class="bg-white p-8 rounded shadow-md w-full max-w-md">
            <h1 class="text-2xl font-bold text-orange-500 mb-6">Ubah Data Admin</h1>
            @php
                // Handle both array and object data structures
                $id = is_array($admin) ? ($admin['id'] ?? 0) : ($admin->id ?? 0);
                $username = is_array($admin) ? ($admin['username'] ?? $admin['email'] ?? '') : ($admin->username_akun_admin ?? $admin->username ?? $admin->email ?? '');
                $name = is_array($admin) ? ($admin['name'] ?? '') : ($admin->nama_lengkap_admin ?? $admin->name ?? '');
                $birthDate = is_array($admin) ? ($admin['birth_date'] ?? '') : ($admin->tanggal_lahir_admin ?? $admin->birth_date ?? '');
                $birthPlace = is_array($admin) ? ($admin['place_of_birth'] ?? '') : ($admin->tempat_lahir_admin ?? $admin->place_of_birth ?? '');
                $phone = is_array($admin) ? ($admin['phone'] ?? '') : ($admin->no_handphone_admin ?? $admin->phone ?? '');
                $memberNumber = is_array($admin) ? ($admin['member_number'] ?? '') : ($admin->no_anggota ?? $admin->member_number ?? '');
                $password = is_array($admin) ? '****' : ($admin->password_akun_admin ?? '****');
            @endphp
            <form id="updateAdminForm" class="space-y-4">
                @csrf
                <input type="hidden" id="adminId" value="{{ $id }}">

                <div>
                    <label class="block mb-1 text-sm font-medium">Username</label>
                    <input type="text" name="username_akun_admin" id="username_akun_admin" value="{{ $username }}"
                        class="w-full px-3 py-2 border rounded bg-gray-100 cursor-not-allowed" readonly disabled />
                    <p class="text-xs text-gray-500 mt-1">Username tidak dapat diubah untuk keamanan sistem</p>
                </div>

                <div>
                    <label class="block mb-1 text-sm font-medium">Nama Lengkap</label>
                    <input type="text" name="nama_lengkap_admin" id="nama_lengkap_admin" value="{{ $name }}"
                        class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-orange-400" />
                </div>

                <div>
                    <label class="block mb-1 text-sm font-medium">Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir_admin" id="tanggal_lahir_admin" value="{{ $birthDate }}"
                        class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-orange-400" />
                </div>

                <div>
                    <label class="block mb-1 text-sm font-medium">Tempat Lahir</label>
                    <input type="text" name="tempat_lahir_admin" id="tempat_lahir_admin" value="{{ $birthPlace }}"
                        class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-orange-400" />
                </div>

                <div>
                    <label class="block mb-1 text-sm font-medium">No Handphone</label>
                    <input type="text" name="no_handphone_admin" id="no_handphone_admin" value="{{ $phone }}"
                        class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-orange-400" />
                </div>

                <div>
                    <label class="block mb-1 text-sm font-medium">No Anggota</label>
                    <input type="text" name="no_anggota" id="no_anggota" value="{{ $memberNumber }}"
                        class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-orange-400" />
                </div>

                <div>
                    <label class="block mb-1 text-sm font-medium">Password</label>
                    <input type="password" name="password_akun_admin" id="password_akun_admin" placeholder="Kosongkan jika tidak ingin mengubah"
                        class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-orange-400" />
                </div>

                <div class="flex space-x-4">
                    <button type="submit"
                        class="bg-orange-500 text-white px-4 py-2 rounded hover:bg-orange-600 transition">Update</button>
                    <a href="/Dataadmin" 
                        class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition">Batal</a>
                </div>
            </form>
        </div>
    </div>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // TICKET #005: CRUD Operations - AJAX Update Admin Function (Issue 5a fix)
        document.getElementById('updateAdminForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const adminId = document.getElementById('adminId').value;
            const formData = new FormData(this);
            
            // Convert FormData to JSON object (exclude readonly username field)
            const updateData = {};
            for (let [key, value] of formData.entries()) {
                // Skip username field since it cannot be updated and disabled fields from form data
                if (key !== 'username_akun_admin' && value.trim() !== '') {
                    updateData[key] = value;
                }
            }

            // Show loading
            Swal.fire({
                title: 'Memperbarui...',
                text: 'Sedang memproses pembaruan data admin',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });

            // Make AJAX PUT request
            fetch(`/api/admin/${adminId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
                },
                body: JSON.stringify(updateData)
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
                        // Redirect back to admin list
                        window.location.href = '/Dataadmin';
                    });
                } else {
                    Swal.fire({
                        title: 'Gagal!',
                        text: data.message || 'Terjadi kesalahan saat memperbarui data',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            })
            .catch(error => {
                console.error('Update admin error:', error);
                Swal.fire({
                    title: 'Error!',
                    text: 'Terjadi kesalahan sistem. Silakan coba lagi.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            });
        });
    </script>

</body>

</html>
