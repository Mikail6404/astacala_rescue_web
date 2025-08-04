<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PenggunaController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\BeritaBencanaController;
use App\Http\Controllers\PelaporanController;
use App\Http\Controllers\AuthAdminController;
use App\Http\Controllers\DashboardController;
use App\Http\Middleware\AdminAuth;
use App\Http\Controllers\ProfileAdminController;
use App\Http\Controllers\AuthRelawanController;



Route::get('/', function () {
    return view('welcome');
});

Route::get('/Home', function () {
    return view('dashboard');
});

Route::get('/Pelaporan', function () {
    return view('data_pelaporan');
});

Route::get('/edit_Pelaporan', function () {
    return view('edit_data_pelaporan');
});

Route::get('/Login', function () {
    return view('login');
});

Route::get('/Register', function () {
    return view('register');
});

Route::get('/Isidataregister', function () {
    return view('isidata_register');
});

// Route::get('/Datapublikasi', function () {
//     return view('data_publikasi');
// });




// membaca data admin
Route::get('/Dataadmin', [AdminController::class, 'controlleradmin']);
// ubah data pengguna
Route::get('/Admin/{id}/ubahadmin', [AdminController::class, 'ubahadmin']);
Route::put('/Admin/{id}', [AdminController::class, 'ubahadmi']);
// menghapus data pengguna
Route::delete('/hapus_admin/{id}', [AdminController::class, 'hapusadmin']);




// membaca data pengguna
Route::get('/Datapengguna', [PenggunaController::class, 'controllerpengguna']);
// ubah data pengguna
Route::get('/Pengguna/{id}/ubahpenggun', [PenggunaController::class, 'ubahpenggun']);
Route::put('/Pengguna/{id}', [PenggunaController::class, 'ubahpeng']);
// menghapus data pengguna
Route::delete('/hapus_pengguna/{id}', [PenggunaController::class, 'hapus']);

Route::middleware('api')
    ->prefix('api')
    ->group(base_path('routes/api.php'));


// membaca data publikasi
Route::get('/publikasi', [BeritaBencanaController::class, 'membacaDataPublikasiBeritaBencana'])->name('berita.index');
Route::get('/publikasi/create', [BeritaBencanaController::class, 'tambahDataPublikasiBeritaBencana'])->name('berita.create');
Route::post('/publikasi/store', [BeritaBencanaController::class, 'simpanTambahPublikasiDataBeritaBencana'])->name('berita.store');
Route::get('/publikasi/edit/{id}', [BeritaBencanaController::class, 'editDataPublikasiBeritaBencana'])->name('berita.edit');
Route::put('/publikasi/update/{id}', [BeritaBencanaController::class, 'simpanEditDataPublikasiBeritaBencana'])->name('berita.update');
Route::delete('/publikasi/delete/{id}', [BeritaBencanaController::class, 'hapusDataPublikasiBeritaBencana'])->name('berita.destroy');
Route::post('/berita/{id}/publish', [BeritaBencanaController::class, 'publishPublikasiBeritaBencana'])->name('berita.publish');
Route::get('/berita-published', [BeritaBencanaController::class, 'apiPublishPublikasiBeritaBencana']);
Route::get('/berita-bencana', [BeritaBencanaController::class, 'getPublished']);
Route::get('/berita-bencanas/{id}', [BeritaBencanaController::class, 'show']);



// membaca data pelaporan

Route::get('/pelaporan', [PelaporanController::class, 'membacaDataPelaporan'])->name('pelaporan.index');
Route::delete('/pelaporan/delete/{id}', [PelaporanController::class, 'menghapusDataPelaporan'])->name('pelaporan.destroy');
Route::get('/notifikasi', [PelaporanController::class, 'menampilkanNotifikasiPelaporanMasuk'])->name('pelaporan.notifikasi');
Route::get('/pelaporan/verifikasi/{id}', [PelaporanController::class, 'memberikanNotifikasiVerifikasi'])->name('pelaporan.verifikasi');
Route::get('/notifikasi/{pengguna_id}', [PelaporanController::class, 'getVerifikasi']);
// Route API untuk Flutter mobile
Route::middleware('api')->group(function () {
    Route::post('/api/pelaporans', [PelaporanController::class, 'store']);
});




// === AUTH ===
Route::get('/login', [AuthAdminController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthAdminController::class, 'processLogin'])->name('admin.login.post');
Route::get('/register', [AuthAdminController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthAdminController::class, 'register'])->name('register');
Route::get('/logout', [AuthAdminController::class, 'logout'])->name('logout');

//route halaman akun profil admin
Route::get('/profil-admin', [ProfileAdminController::class, 'show'])->name('profil.admin')->middleware(AdminAuth::class);
// Tampilkan form edit profil admin
Route::get('/profil-admin/edit', [ProfileAdminController::class, 'edit'])->name('profil.admin.edit')->middleware(AdminAuth::class);
// Simpan perubahan data profil admin
Route::put('/profil-admin/update', [ProfileAdminController::class, 'update'])->name('profil.admin.update')->middleware(AdminAuth::class);

// === PROTECTED ===
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard')->middleware(AdminAuth::class);



// Test authentication route (no CSRF for testing)
Route::post('/test-auth', function (Illuminate\Http\Request $request) {
    $authController = new AuthAdminController(new App\Services\GibranAuthService(new App\Services\AstacalaApiClient()));
    return $authController->processLogin($request);
})->name('test.auth')->withoutMiddleware('web');

// Test authentication page
Route::get('/auth-test', function () {
    return view('auth-test');
});

// Debug route to check session
Route::get('/debug-session', function () {
    return response()->json([
        'session_data' => session()->all(),
        'admin_id' => session('admin_id'),
        'admin_username' => session('admin_username'),
        'admin_name' => session('admin_name'),
        'admin_email' => session('admin_email'),
        'access_token' => session('access_token') ? 'present' : 'missing'
    ]);
})->withoutMiddleware('web');

Route::get('/Logout', function () {
    return view('login');
});
