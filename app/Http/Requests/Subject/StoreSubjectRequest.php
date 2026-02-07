<?php

namespace App\Http\Requests\Subject;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Store subject request validation.
 * Per 07_api_specification.md §3.2
 */
class StoreSubjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'date_of_birth' => ['required', 'date', 'before_or_equal:today'],
            'gender' => ['required', Rule::in(['L', 'P'])],
            'nik' => ['nullable', 'string', 'size:16'],
            'address' => ['nullable', 'string', 'max:500'],
            'parent_name' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama wajib diisi',
            'name.min' => 'Nama minimal 3 karakter',
            'date_of_birth.required' => 'Tanggal lahir wajib diisi',
            'date_of_birth.date' => 'Format tanggal lahir tidak valid',
            'date_of_birth.before_or_equal' => 'Tanggal lahir tidak boleh di masa depan',
            'gender.required' => 'Jenis kelamin wajib diisi',
            'gender.in' => 'Jenis kelamin harus L atau P',
            'nik.size' => 'NIK harus 16 digit',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'normalized_name' => strtoupper(trim(preg_replace('/\s+/', ' ', $this->name ?? ''))),
            'user_id' => auth()->id(),
        ]);
    }
}
