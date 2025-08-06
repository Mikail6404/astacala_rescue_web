<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Profil Saya</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-white p-10">
    <h1 class="text-2xl font-bold text-red-700 mb-8 text-center">Profil Saya</h1>

    <div class="max-w-xl mx-auto">
        <label>Username Admin</label>
        <input type="text" class="w-full p-2 bg-gray-300 rounded mb-4" value="{{ $admin->username_akun_admin }}"
            readonly>

        <label>Nama Lengkap</label>
        <input type="text" class="w-full p-2 bg-gray-300 rounded mb-4" value="{{ $admin->nama_lengkap_admin }}"
            readonly>

        <label>Tanggal Lahir</label>
        <div class="relative mb-4">
            <input type="text" class="w-full p-2 bg-gray-300 rounded" value="{{ $admin->tanggal_lahir_admin ?: '-' }}"
                readonly>
            <span class="absolute right-3 top-2.5">
                📅
            </span>
        </div>

        <label>Tempat Lahir</label>
        <input type="text" class="w-full p-2 bg-gray-300 rounded mb-4" value="{{ $admin->tempat_lahir_admin ?: '-' }}"
            readonly>

        <label>No Handphone</label>
        <input type="text" class="w-full p-2 bg-gray-300 rounded mb-4" value="{{ $admin->no_handphone_admin ?: '-' }}"
            readonly>

        <label>No Anggota</label>
        <input type="text" class="w-full p-2 bg-gray-300 rounded mb-6" value="{{ $admin->no_anggota ?: '-' }}" readonly>

        <div class="flex space-x-4">
            <a href="{{ route('profil.admin.edit') }}" class="bg-red-600 text-white px-4 py-2 rounded inline-block">
                Edit Profil
            </a>
            <a href="/dashboard" class="bg-gray-600 text-white px-4 py-2 rounded inline-block">
                Kembali ke Dashboard
            </a>
        </div>
    </div>
</body>

</html>
