<?php

namespace App\Http\Requests\Measurement;

use App\Services\SubjectService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMeasurementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $measurement = $this->route('measurement');
        $subject = $measurement?->subject;
        $category = $this->determineCategory($subject);

        $rules = [
            'measurement_date' => ['required', 'date', 'before_or_equal:today'],
            'weight' => ['required', 'numeric'],
            'height' => ['required', 'numeric'],
            'arm_circumference' => ['nullable', 'numeric', 'min:10', 'max:60'],
            'is_pregnant' => ['nullable', 'boolean'],
            'pregnancy_start_date' => ['nullable', 'date', 'before_or_equal:today'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];

        switch ($category) {
            case 'balita':
                $rules['weight'][] = 'min:1';
                $rules['weight'][] = 'max:45';
                $rules['height'][] = 'min:30';
                $rules['height'][] = 'max:135';
                $rules['head_circumference'] = ['nullable', 'numeric', 'min:10', 'max:60'];
                $rules['measurement_type'] = ['nullable', Rule::in(['berbaring', 'berdiri'])];
                break;

            case 'remaja':
                $rules['weight'][] = 'min:8';
                $rules['weight'][] = 'max:220';
                $rules['height'][] = 'min:75';
                $rules['height'][] = 'max:230';
                break;

            case 'dewasa':
                $rules['weight'][] = 'min:15';
                $rules['weight'][] = 'max:400';
                $rules['height'][] = 'min:90';
                $rules['height'][] = 'max:260';
                $rules['waist_circumference'] = ['nullable', 'numeric', 'min:30', 'max:200'];
                break;
        }

        return $rules;
    }

    private function determineCategory($subject): string
    {
        if (!$subject) {
            return 'balita';
        }

        $measurementDate = $this->input('measurement_date', now()->toDateString());
        $ageInMonths = app(SubjectService::class)->calculateAgeInMonths(
            $subject->date_of_birth,
            $measurementDate
        );

        if ($ageInMonths < 60) {
            return 'balita';
        }

        if ($ageInMonths < 216) {
            return 'remaja';
        }

        return 'dewasa';
    }
}
