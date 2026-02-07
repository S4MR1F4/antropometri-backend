<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Reference table for BB/TB (Berat Badan per Tinggi Badan) - Balita
 * Per 06_data_dictionary.md §5.3
 * 
 * Read-only model for Z-score lookup.
 * NOTE: This table uses height instead of age for lookup.
 */
class ReferenceBalitaBbtb extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'reference_balita_bbtb';

    /**
     * Indicates if the model should be timestamped.
     */
    public $timestamps = false;

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'height' => 'decimal:1',
        'neg3sd' => 'decimal:3',
        'neg2sd' => 'decimal:3',
        'neg1sd' => 'decimal:3',
        'median' => 'decimal:3',
        'pos1sd' => 'decimal:3',
        'pos2sd' => 'decimal:3',
        'pos3sd' => 'decimal:3',
    ];

    /**
     * Find reference by gender and height.
     * Height is rounded to nearest 0.5 for lookup.
     */
    public static function findByGenderAndHeight(string $gender, float $height): ?self
    {
        // Round height to nearest 0.5
        $roundedHeight = round($height * 2) / 2;

        return static::where('gender', $gender)
            ->where('height', $roundedHeight)
            ->first();
    }
}
