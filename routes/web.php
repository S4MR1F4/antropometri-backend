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

Route::get('/', function () {
    return view('landing');
});

Route::any('{any}', function () {
    return response()->json([
        'success' => false,
        'message' => 'Web access is restricted. Please use the API endpoints at /api.',
    ], 403);
})->where('any', '^(?!api).*$');
