<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

class VersionController extends Controller
{
    public function check()
    {
        $latestVersion = env('APP_LATEST_VERSION', '2.0.0');
        $minVersion = env('APP_MIN_SUPPORTED_VERSION', '2.0.0');

        return response()->json([
            'status' => 'success',
            'data' => [
                'latest_version' => $latestVersion,
                'min_version' => $minVersion,
                'force_update' => filter_var(env('APP_FORCE_UPDATE', false), FILTER_VALIDATE_BOOLEAN),
                'play_store_url' => env(
                    'APP_PLAY_STORE_URL',
                    'https://play.google.com/store/apps/details?id=com.samrifa.antropometri'
                ),
                'market_url' => env('APP_MARKET_URL', 'market://details?id=com.samrifa.antropometri'),
                'release_notes' => env(
                    'APP_RELEASE_NOTES',
                    'Pembaruan Antropometri tersedia. Update untuk mendapatkan perbaikan stabilitas, sinkronisasi, dan keamanan data terbaru.'
                ),
            ]
        ]);
    }
}
