<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class VersionController extends Controller
{
    public function check()
    {
        // This acts as the single source of truth for the app version.
        // In a real production system, this could be stored in the database.
        return response()->json([
            'status' => 'success',
            'data' => [
                'latest_version' => '1.0.0',
                'min_version' => '1.0.0', // Versions below this force update
                'force_update' => false,
                'play_store_url' => 'market://details?id=com.samrifa.antropometri',
                'release_notes' => 'Pembaruan aplikasi Antropometri! Kami telah menambahkan fitur import data, perbaikan navigasi, dan peningkatan performa.',
            ]
        ]);
    }
}
