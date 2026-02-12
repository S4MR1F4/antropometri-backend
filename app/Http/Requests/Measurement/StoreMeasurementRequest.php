<?php

namespace App\Http\Requests\Measurement;

use App\Services\SubjectService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Store measurement request validation.
 * Per 07_api_specification.md §4.1 and 08_calculation_logic.md §6
 * 
 * Validation rules vary by category (balita/remaja/dewasa).
 */
class StoreMeasurementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $subject = $this->route('subject');
        $category = $this->determineCategory($subject);

        $rules = [
            'measurement_date' => ['required', 'date', 'before_or_equal:today'],
            'weight' => ['required', 'numeric'],
            'height' => ['required', 'numeric'],
            'arm_circumference' => ['nullable', 'numeric', 'min:10', 'max:60'],
            'is_pregnant' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];

        // Category-specific validation per 08_calculation_logic.md §6.1
        switch ($category) {
            case 'balita':
                $rules['weight'][] = 'min:0.5';
                $rules['weight'][] = 'max:50';
                $rules['height'][] = 'min:30';
                $rules['height'][] = 'max:150';
                $rules['head_circumference'] = ['nullable', 'numeric', 'min:10', 'max:60'];
                $rules['measurement_type'] = ['nullable', Rule::in(['berbaring', 'berdiri'])];
                break;

            case 'remaja':
                $rules['weight'][] = 'min:5';
                $rules['weight'][] = 'max:200';
                $rules['height'][] = 'min:50';
                $rules['height'][] = 'max:250';
                break;

            case 'dewasa':
                $rules['weight'][] = 'min:20';
                $rules['weight'][] = 'max:500';
                $rules['height'][] = 'min:100';
                $rules['height'][] = 'max:300';
                $rules['waist_circumference'] = ['nullable', 'numeric', 'min:30', 'max:200'];
                break;
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'measurement_date.required' => 'Tanggal pengukuran wajib diisi',
            'measurement_date.date' => 'Format tanggal tidak valid',
            'measurement_date.before_or_equal' => 'Tanggal pengukuran tidak boleh di masa depan',
            'weight.required' => 'Berat badan wajib diisi',
            'weight.numeric' => 'Berat badan harus berupa angka',
            'weight.min' => 'Berat badan terlalu kecil',
            'weight.max' => 'Berat badan terlalu besar',
            'height.required' => 'Tinggi badan wajib diisi',
            'height.numeric' => 'Tinggi badan harus berupa angka',
            'height.min' => 'Tinggi badan terlalu kecil',
            'height.max' => 'Tinggi badan terlalu besar',
            'head_circumference.numeric' => 'Lingkar kepala harus berupa angka',
            'waist_circumference.numeric' => 'Lingkar perut harus berupa angka',
        ];
    }

    /**
     * Determine category based on subject's age.
     */
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
        } elseif ($ageInMonths < 216) {
            return 'remaja';
        }

        return 'dewasa';
    }
}
