<?php

namespace App\Models;

use App\Models\Concerns\BelongsToUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Measurement model per 06_data_dictionary.md §4
 * 
 * Stores anthropometric measurements and calculated results.
 * Categories: balita, remaja, dewasa
 */
class Measurement extends Model
{
    use BelongsToUser, HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'subject_id',
        'user_id',
        'measurement_date',
        'category',
        'weight',
        'height',
        'head_circumference',
        'waist_circumference',
        'arm_circumference',
        'is_pregnant',
        'measurement_type',
        'age_in_months',
        'age_in_years',
        'bmi',
        'zscore_bbu',
        'zscore_tbu',
        'zscore_bbtb',
        'zscore_imtu',
        'status_bbu',
        'status_tbu',
        'status_bbtb',
        'status_imtu',
        'status_bmi',
        'has_central_obesity',
        'notes',
        'reference_data',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'measurement_date' => 'date',
        'weight' => 'decimal:2',
        'height' => 'decimal:2',
        'head_circumference' => 'decimal:2',
        'waist_circumference' => 'decimal:2',
        'arm_circumference' => 'decimal:2',
        'bmi' => 'decimal:2',
        'zscore_bbu' => 'decimal:2',
        'zscore_tbu' => 'decimal:2',
        'zscore_bbtb' => 'decimal:2',
        'zscore_imtu' => 'decimal:2',
        'is_pregnant' => 'boolean',
        'has_central_obesity' => 'boolean',
        'reference_data' => 'array',
    ];

    /**
     * Get the subject this measurement belongs to.
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }
}
