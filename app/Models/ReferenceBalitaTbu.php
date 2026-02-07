<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Reference table for TB/U (Tinggi Badan per Umur) - Balita
 * Per 06_data_dictionary.md §5.2
 * 
 * Read-only model for Z-score lookup.
 */
class ReferenceBalitaTbu extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'reference_balita_tbu';

    /**
     * Indicates if the model should be timestamped.
     */
    public $timestamps = false;

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'neg3sd' => 'decimal:3',
        'neg2sd' => 'decimal:3',
        'neg1sd' => 'decimal:3',
        'median' => 'decimal:3',
        'pos1sd' => 'decimal:3',
        'pos2sd' => 'decimal:3',
        'pos3sd' => 'decimal:3',
    ];

    /**
     * Find reference by gender and age in months.
     */
    public static function findByGenderAndAge(string $gender, int $ageMonths): ?self
    {
        return static::where('gender', $gender)
            ->where('age_months', $ageMonths)
            ->first();
    }
}
