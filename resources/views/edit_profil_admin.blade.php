<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit Profil</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-white p-10">
    <h1 class="text-2xl font-bold text-red-700 mb-8 text-center">Edit Profil</h1>

    <form action="{{ route('profil.admin.update') }}" method="POST" class="max-w-xl mx-auto">
        @csrf
        @method('PUT')

        @php
            // Handle both array and object data structures
            $username = is_array($admin) ? ($admin['username_akun_admin'] ?? '') : ($admin->username_akun_admin ?? '');
            $name = is_array($admin) ? ($admin['nama_lengkap_admin'] ?? '') : ($admin->nama_lengkap_admin ?? '');
            $birthDate = is_array($admin) ? ($admin['tanggal_lahir_admin'] ?? '') : ($admin->tanggal_lahir_admin ?? '');
            $birthPlace = is_array($admin) ? ($admin['tempat_lahir_admin'] ?? '') : ($admin->tempat_lahir_admin ?? '');
            $phone = is_array($admin) ? ($admin['no_handphone_admin'] ?? '') : ($admin->no_handphone_admin ?? '');
            $memberNumber = is_array($admin) ? ($admin['no_anggota'] ?? '') : ($admin->no_anggota ?? '');
        @endphp

        <label>Username Admin</label>
        <input type="text" name="username_akun_admin" class="w-full p-2 bg-gray-300 rounded mb-4"
            value="{{ $username }}">

        <label>Nama Lengkap</label>
        <input type="text" name="nama_lengkap_admin" class="w-full p-2 bg-gray-300 rounded mb-4"
            value="{{ $name }}">

        <label>Tanggal Lahir</label>
        <input type="date" name="tanggal_lahir_admin" class="w-full p-2 bg-gray-300 rounded mb-4"
            value="{{ $birthDate }}">

        <label>Tempat Lahir</label>
        <input type="text" name="tempat_lahir_admin" class="w-full p-2 bg-gray-300 rounded mb-4"
            value="{{ $birthPlace }}">

        <label>No Handphone</label>
        <input type="text" name="no_handphone_admin" class="w-full p-2 bg-gray-300 rounded mb-4"
            value="{{ $phone }}">

        <label>No Anggota</label>
        <input type="text" name="no_anggota" class="w-full p-2 bg-gray-300 rounded mb-6"
            value="{{ $memberNumber }}">

        <button type="submit" class="bg-red-600 text-white px-6 py-2 rounded">
            Simpan
        </button>
    </form>
</body>

</html>
