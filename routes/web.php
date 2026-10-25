<?php

use Illuminate\Support\Facades\Route;
use App\Support\MobileLink;

Route::get('/.well-known/assetlinks.json', function () {
    $fingerprints = config('app.android_sha256_cert_fingerprints', []);

    return response()->json([
        [
            'relation' => [
                'delegate_permission/common.handle_all_urls',
            ],
            'target' => [
                'namespace' => 'android_app',
                'package_name' => config('app.android_package_name'),
                'sha256_cert_fingerprints' => array_values($fingerprints),
            ],
        ],
    ]);
});

Route::get('/privacy-policy', function () {
    return view('privacy-policy');
});

Route::get('/login', function () {
    return view('app-link-fallback', [
        'title' => 'Login ke Aplikasi',
        'message' => 'Membuka aplikasi Antropometri untuk login.',
        'appUrl' => MobileLink::schemeUrl('/login', request()->query()),
        'playStoreUrl' => config('app.mobile_play_store_url'),
    ]);
});

Route::get('/password/reset/{token}', function (string $token) {
    return view('app-link-fallback', [
        'title' => 'Reset Kata Sandi',
        'message' => 'Membuka aplikasi Antropometri untuk reset kata sandi.',
        'appUrl' => MobileLink::schemeUrl('/password/reset/' . $token, request()->query()),
        'playStoreUrl' => config('app.mobile_play_store_url'),
    ]);
});

Route::get('/measurements/{measurement}', function (string $measurement) {
    return view('app-link-fallback', [
        'title' => 'Detail Pemeriksaan',
        'message' => 'Membuka detail pemeriksaan di aplikasi Antropometri.',
        'appUrl' => MobileLink::schemeUrl('/measurements/' . $measurement, request()->query()),
        'playStoreUrl' => config('app.mobile_play_store_url'),
    ]);
});

Route::middleware(['throttle:60,1'])->group(function () {
    Route::get('/card/patient/{subject}', [\App\Http\Controllers\PatientCardController::class, 'show'])->name('patient.card');
    Route::get('/card/measurement/{measurement}', [\App\Http\Controllers\PatientCardController::class, 'showMeasurement'])->name('measurement.card');
    Route::get('/patient/{subject}', [\App\Http\Controllers\PatientCardController::class, 'show'])->name('patient.card.alias');
});

Route::get('/', [\App\Http\Controllers\LandingPageController::class, 'index']);
Route::redirect('/dashboard', '/', 301);

Route::get('/downloads/Template_Perhitungan_Antropometri.xlsx', function () {
    $path = public_path('downloads/Template_Perhitungan_Antropometri.xlsx');
    if (!file_exists($path)) {
        abort(404, 'Template Excel belum tersedia.');
    }
    return response()->download($path, 'Template_Perhitungan_Antropometri.xlsx', [
        'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'Content-Disposition' => 'attachment; filename="Template_Perhitungan_Antropometri.xlsx"',
    ]);
});

Route::get('/downloads/antropometri-latest.apk', function () {
    $path = public_path('downloads/antropometri-latest.apk');
    if (!file_exists($path)) {
        abort(404, 'File APK belum tersedia.');
    }
    return response()->download($path, 'antropometri-v2.2.0.apk', [
        'Content-Type' => 'application/vnd.android.package-archive',
        'Content-Disposition' => 'attachment; filename="antropometri-v2.2.0.apk"',
    ]);
});

Route::get('/downloads/playstore', function () {
    $playStoreUrl = config('app.mobile_play_store_url', 'https://play.google.com/store/apps/details?id=com.samrifa.antropometri');
    return redirect()->away($playStoreUrl);
});

Route::any('{any}', function () {
    return response()->json([
        'success' => false,
        'message' => 'Web access is restricted. Please use the API endpoints at /api.',
    ], 403);
})->where('any', '^(?!api).*$');
