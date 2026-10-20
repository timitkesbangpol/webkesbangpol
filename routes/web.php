<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Import Controllers
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\OtpController;

// Landing Page Controllers
use App\Http\Controllers\LandingPage\LandingpageController;
use App\Http\Controllers\LandingPage\LandingpageOrmasController;
use App\Http\Controllers\LandingPage\LandingpagePotensiKonflikController;
use App\Http\Controllers\LandingPage\LandingpageProfileController;
use App\Http\Controllers\LandingPage\LandingpageSakipController;
use App\Http\Controllers\LandingPage\LandingpageMitraController;
use App\Http\Controllers\LandingPage\DocumentViewerController;
use App\Http\Controllers\LandingPage\ElectionController;

// Admin/Dashboard Controllers
use App\Http\Controllers\Admin\Content\BannerController;
use App\Http\Controllers\Admin\Profile\BidangController;
use App\Http\Controllers\Admin\Content\GaleriController;
use App\Http\Controllers\Admin\Sakip\IkuController;
use App\Http\Controllers\Admin\Sakip\LaporanAkipController;
use App\Http\Controllers\Admin\Sakip\LaporanKajianController;
use App\Http\Controllers\Admin\Profile\LandasanHukumController;
use App\Http\Controllers\Admin\Content\MitraController;
use App\Http\Controllers\Admin\Informasi\OrmasController;
use App\Http\Controllers\Admin\Pemilu\PemiluRayaController;
use App\Http\Controllers\Admin\Pemilu\PilpresController;
use App\Http\Controllers\Admin\Content\PostController;
use App\Http\Controllers\Admin\Informasi\PotensiKonflikController;
use App\Http\Controllers\Admin\Profile\ProgramController;
use App\Http\Controllers\Admin\Sakip\RenjaController;
use App\Http\Controllers\Admin\Sakip\RenstraController;
use App\Http\Controllers\Admin\Profile\StrukturController;
use App\Http\Controllers\Admin\Sakip\UkurKerjaController;
use App\Http\Controllers\Admin\Profile\VisiMisiController;
use App\Http\Controllers\Admin\Pemilu\WalikotaController;
use App\Http\Controllers\Admin\Pemilu\LegislatifController;

//========================================================================
// AUTHENTICATION ROUTES
//========================================================================
Route::get('/captcha', [LoginController::class, 'captcha'])->name('captcha');
Route::get('/reload-captcha', [LoginController::class, 'reloadCaptcha'])->name('captcha.reload');
$adminLoginPath = env('ADMIN_LOGIN_PATH', 'kelola-bakesbangpol');

// Rute Login Resmi Admin (Secret Path)
Route::get($adminLoginPath, [LoginController::class, 'showLoginForm'])->name('login');
Route::post($adminLoginPath, [LoginController::class, 'login'])->middleware('throttle:10,1')->name('login.process');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Blokir /login default agar bot & scanner langsung mendapat 404 Not Found
Route::any('/login', function () {
    abort(404);
});
Route::any('/admin/login', function () {
    abort(404);
});

//========================================================================
// ROUTE SISTEM OTP VERIFIKASI 2FA
//========================================================================
Route::get('/verify-otp', [OtpController::class, 'showVerifyForm'])->name('otp.verify');
Route::post('/verify-otp', [OtpController::class, 'verify'])->middleware('throttle:15,1')->name('otp.process');
Route::post('/resend-otp', [OtpController::class, 'resend'])->middleware('throttle:4,1')->name('otp.resend');


//========================================================================
// LANDING PAGE ROUTES (PUBLIC)
//========================================================================
Route::get('/sitemap.xml', function () {
    $posts = \App\Models\Post::whereNotNull('slug')
        ->where('slug', '!=', '')
        ->latest('updated_at')
        ->get();

    return response()
        ->view('landingpage.sitemap', compact('posts'))
        ->header('Content-Type', 'text/xml; charset=utf-8');
})->name('sitemap');

Route::get('/', [LandingpageController::class, 'index'])->name('beranda');
Route::get('/artikel/{slug}', [LandingpageController::class, 'isiArtikel'])->name('isi-artikel');
Route::get('/articles', [LandingpageController::class, 'semuaArtikel'])->name('semua-artikel');
Route::get('/filter-artikel', [LandingpageController::class, 'filterArtikel'])->name('filter-artikel');

// LANDING PAGE - PROFILE
Route::prefix('profile')->group(function () {
    Route::get('/', [LandingpageProfileController::class, 'tampilMenuProfile'])->name('tampilmenuprofile');
    Route::get('/visimisi', [LandingpageProfileController::class, 'tampilVisiMisi'])->name('tampilvisimisi');
    Route::get('/tugas-fungsi', [LandingpageProfileController::class, 'tampilTugasFungsi'])->name('tampiltugasfungsi');
    Route::get('/struktur-organisasi', [LandingpageProfileController::class, 'tampilStruktur'])->name('tampilstruktur');
    Route::get('/dasar-hukum', [LandingpageProfileController::class, 'tampilDasarHukum'])->name('tampildasarhukum');
    Route::get('/program', [LandingpageProfileController::class, 'tampilProgram'])->name('tampilprogram');
    Route::get('/sejarah', [LandingpageProfileController::class, 'tampilSejarah'])->name('tampilsejarah');
});

// LANDING PAGE - SAKIP (Sistem Akuntabilitas Kinerja Instansi Pemerintah)
Route::prefix('sakip')->group(function () {
    Route::get('/', [LandingpageSakipController::class, 'tampilMenuSakip'])->name('tampilmenusakip');
    Route::get('/rencana-kerja', [LandingpageSakipController::class, 'tampilRenja'])->name('tampilrenja');
    Route::get('/rencana-strategis', [LandingpageSakipController::class, 'tampilRenstra'])->name('tampilrenstra');
    Route::get('/indikator-kinerja', [LandingpageSakipController::class, 'tampilIku'])->name('tampiliku');
    Route::get('/pengukuran-kerja', [LandingpageSakipController::class, 'tampilUkurkerja'])->name('tampilukurkerja');
    Route::get('/laporan-akip', [LandingpageSakipController::class, 'tampillAkip'])->name('tampillakip');
    Route::get('/laporan-kajian', [LandingpageSakipController::class, 'tampilLaporanKajian'])->name('tampillaporankajian');
});

// LANDING PAGE - INFORMASI
Route::get('/data-organisasi-masyarakat', [LandingpageOrmasController::class, 'tampilDataOrmas'])->name('tampil-data-ormas');
Route::redirect('/jumlah-potensi-konflik', '/')->name('tampil-jumlah-potensi-konflik');

// LANDING PAGE - MITRA
Route::get('/mitra', [LandingpageMitraController::class, 'tampilMitra'])->name('tampilmitra');
Route::get('/mitra/{kategori}', [LandingpageMitraController::class, 'detailMitra'])->name('mitra.detail');
Route::get('/mitra/detail/{mitra}', [LandingpageController::class, 'showMitraDetail'])->name('mitra.show.detail');

// LANDING PAGE - PEMILU
Route::get('/pemilu', [ElectionController::class, 'index'])->name('pemilu.index');
Route::get('/pemilu/{kategori}', [ElectionController::class, 'show'])->name('pemilu.show');
Route::get('/pemilu/{kategori}/{id}', [ElectionController::class, 'detail'])->name('pemilu.detail');

//========================================================================
// ADMIN/DASHBOARD ROUTES (PROTECTED BY AUTH MIDDLEWARE)
//========================================================================
Route::middleware(['auth'])->group(function () {
    // DASHBOARD
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    // STRUKTUR ORGANISASI ROUTES
    Route::get('/strukturors', [StrukturController::class, 'index'])->name('strukturors.index');
    Route::post('/strukturors/save-layout', [StrukturController::class, 'saveLayout'])->name('strukturors.save-layout');
    Route::post('/strukturors/store-box', [StrukturController::class, 'storeBox'])->name('strukturors.store-box');
    Route::match(['PUT', 'PATCH', 'POST'], '/strukturors-builder/update/{id}', [StrukturController::class, 'updateBox'])->name('strukturors.update-box');
    Route::delete('/strukturors-builder/delete/{id}', [StrukturController::class, 'deleteBox'])->name('strukturors.delete-box');

    Route::resource('/posts', PostController::class);
    Route::resource('/galeris', GaleriController::class);
    Route::resource('/banners', BannerController::class);

    // PROFILE MANAGEMENT
    Route::resource('/visimisis', VisiMisiController::class);
    Route::resource('/programs', ProgramController::class);
    Route::get('/get-programs-by-bidang', [ProgramController::class, 'getProgramsByBidang'])->name('get-programs-by-bidang');
    Route::resource('/bidangs', BidangController::class);
    Route::resource('/landasanhukum', LandasanHukumController::class);

    // SAKIP MANAGEMENT
    Route::resource('/renja', RenjaController::class);
    Route::resource('/renstra', RenstraController::class);
    Route::resource('/iku', IkuController::class);
    Route::resource('/ukurkerja', UkurKerjaController::class);
    Route::resource('/lakip', LaporanAkipController::class);
    Route::resource('/laporankajian', LaporanKajianController::class);
    
    // MITRA MANAGEMENT
    Route::resource('/mitras', MitraController::class);

    // INFORMASI MANAGEMENT
    // -- Ormas
    Route::resource('/ormass', OrmasController::class);
    Route::post('/ormass/manual', [OrmasController::class, 'inputManualStore'])->name('ormass.inputmanualstore');
    Route::get('/ormass-import-history', [OrmasController::class, 'importHistory'])->name('ormass.import-history');
    Route::delete('/ormass-import-history/{batchId}/rollback', [OrmasController::class, 'rollbackBatch'])->name('ormass.rollback-batch');

    // -- Potensi Konflik Management (Dinonaktifkan sementara)
    Route::redirect('/potensi-konflik{any}', '/admin/home')->where('any', '.*');

    Route::prefix('admin')->name('admin.')->group(function () {
        // Dashboard Admin
        Route::get('/home', [HomeController::class, 'index'])->name('home');
        Route::get('/pemilu-raya', [PemiluRayaController::class, 'index'])->name('pemilu-raya.dashboard');

        // Manajemen Pemilu
        Route::prefix('pemilu')->name('pemilu.')->group(function () {
            Route::resource('pilpres', PilpresController::class);
            Route::resource('walikota', WalikotaController::class);
            
            // Manajemen Legislatif
            Route::delete('legislatif/destroy-all', [LegislatifController::class, 'destroyAll'])->name('legislatif.destroy.all');
            Route::get('legislatif/import', [LegislatifController::class, 'showImportForm'])->name('legislatif.import.form');
            Route::post('legislatif/import', [LegislatifController::class, 'import'])->name('legislatif.import');
            Route::resource('legislatif', LegislatifController::class);
        });
    });
});

//========================================================================
// DOCUMENT VIEWER ROUTES (SECURE FILE STREAMING)
//========================================================================
Route::get('/file-content-renja/{filename}', [DocumentViewerController::class, 'renja'])->name('file.renja');
Route::get('/file-content-renstra/{filename}', [DocumentViewerController::class, 'renstra'])->name('file.renstra');
Route::get('/file-content-iku/{filename}', [DocumentViewerController::class, 'iku'])->name('file.iku');
Route::get('/file-content-ukurkerja/{filename}', [DocumentViewerController::class, 'ukurkerja'])->name('file.ukurkerja');
Route::get('/file-content-lakip/{filename}', [DocumentViewerController::class, 'lakip'])->name('file.lakip');
Route::get('/file-content-laporankajian/{filename}', [DocumentViewerController::class, 'laporankajian'])->name('file.laporankajian');

