<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login Page</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="flex h-screen">

    <!-- Bagian Form Login -->
    <div class="w-1/2 flex items-center justify-center bg-gray-200">
        <div class="bg-gray-300 p-16 rounded-lg shadow-md w-[500px]">
            <h1 class="text-center text-3xl font-bold mb-8">Login</h1>

            <form method="POST" action="{{ route('admin.login.post') }}">
                @csrf

                <div class="mb-6">
                    <label class="block text-sm mb-2 font-semibold">Username</label>
                    <input type="text" name="username" placeholder="Masukkan Username Anda"
                        class="w-full px-4 py-3 border rounded focus:outline-none focus:ring-2 focus:ring-red-500"
                        required>
                </div>

                <div class="mb-8">
                    <label class="block text-sm mb-2 font-semibold">Password</label>
                    <input type="password" name="password" placeholder="Masukkan Password Anda"
                        class="w-full px-4 py-3 border rounded focus:outline-none focus:ring-2 focus:ring-red-500"
                        required>
                </div>

                <button type="submit"
                    class="w-full bg-red-700 hover:bg-red-800 text-white font-bold py-3 px-4 rounded">
                    Masuk
                </button>
            </form>

            <a href="/register" class="block text-center text-red-700 mt-6 text-sm hover:underline">Daftar Akun</a>
        </div>
    </div>

    <div class="w-1/2 h-full">
        <img src="{{ asset('image/imagelogin.jpg') }}" alt="Login Image" class="object-cover w-full h-full">
    </div>

</body>

</html>
