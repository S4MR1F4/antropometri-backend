<?php

namespace App\Http\Controllers;

use App\Models\Measurement;
use App\Models\Subject;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PatientCardController extends Controller
{
    /**
     * Display public patient health card with full history and QR verification.
     * Enforces unguessable cryptographic card_token for public access, preventing
     * integer enumeration and brute-force attacks.
     */
    public function show(Request $request, int|string $subject): View
    {
        $subjectModel = null;

        // 1. First priority: Look up by unguessable card_token (UUID/crypto token)
        $subjectModel = Subject::withTrashed()
            ->with(['measurements' => function ($query) {
                $query->orderBy('measurement_date', 'desc')
                    ->orderBy('created_at', 'desc');
            }, 'user'])
            ->where('card_token', (string) $subject)
            ->first();

        // 2. Fallback for numeric IDs: ONLY allow if authenticated user or signed URL
        if (!$subjectModel && is_numeric($subject)) {
            if ($request->user() || $request->hasValidSignature()) {
                $subjectModel = Subject::withTrashed()
                    ->with(['measurements' => function ($query) {
                        $query->orderBy('measurement_date', 'desc')
                            ->orderBy('created_at', 'desc');
                    }, 'user'])
                    ->find($subject);
            }
        }

        if (!$subjectModel) {
            abort(404, 'Kartu Pasien Tidak Ditemukan. Pastikan tautan kartu valid atau scan kembali QR resmi.');
        }

        $measurements = $subjectModel->measurements;
        $latest = $measurements->first();

        // Calculate age
        $dob = $subjectModel->date_of_birth;
        $currentAgeMonths = $dob ? (int) $dob->diffInMonths(now()) : 0;
        $currentAgeYears = $dob ? (int) $dob->diffInYears(now()) : 0;

        $cardUrl = route('patient.card', ['subject' => $subjectModel->getSecureCardToken()]);

        return view('patient-card', [
            'subject' => $subjectModel,
            'measurements' => $measurements,
            'latest' => $latest,
            'currentAgeMonths' => $currentAgeMonths,
            'currentAgeYears' => $currentAgeYears,
            'cardUrl' => $cardUrl,
        ]);
    }

    /**
     * Display card directly from a measurement reference, redirecting to the secure token URL.
     */
    public function showMeasurement(Request $request, int|string $measurement): RedirectResponse
    {
        $measurementModel = Measurement::withTrashed()->with('subject')->find($measurement);

        if (!$measurementModel || !$measurementModel->subject) {
            abort(404, 'Data Pemeriksaan Tidak Ditemukan.');
        }

        $secureToken = $measurementModel->subject->getSecureCardToken();

        return redirect()->route('patient.card', ['subject' => $secureToken]);
    }
}
