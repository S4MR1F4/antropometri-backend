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
     */
    public function show(Request $request, int|string $subject): View
    {
        $subjectModel = Subject::withTrashed()
            ->with(['measurements' => function ($query) {
                $query->orderBy('measurement_date', 'desc')
                    ->orderBy('created_at', 'desc');
            }, 'user'])
            ->find($subject);

        if (!$subjectModel) {
            abort(404, 'Kartu Pasien Tidak Ditemukan. Pastikan data pasien telah terdaftar dan tersinkronisasi.');
        }

        $measurements = $subjectModel->measurements;
        $latest = $measurements->first();

        // Calculate age
        $dob = $subjectModel->date_of_birth;
        $currentAgeMonths = $dob ? (int) $dob->diffInMonths(now()) : 0;
        $currentAgeYears = $dob ? (int) $dob->diffInYears(now()) : 0;

        $cardUrl = route('patient.card', ['subject' => $subjectModel->id]);

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
     * Display card directly from a measurement ID reference.
     */
    public function showMeasurement(int|string $measurement): RedirectResponse
    {
        $measurementModel = Measurement::withTrashed()->find($measurement);

        if (!$measurementModel || !$measurementModel->subject_id) {
            abort(404, 'Data Pemeriksaan Tidak Ditemukan.');
        }

        return redirect()->route('patient.card', ['subject' => $measurementModel->subject_id]);
    }
}
