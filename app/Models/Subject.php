<?php

namespace App\Models;

use App\Models\Concerns\BelongsToUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Subject model per 06_data_dictionary.md §3
 * 
 * Represents an individual being measured (child, adolescent, or adult).
 * Data is owned by the user (petugas) who created it.
 */
class Subject extends Model
{
    use BelongsToUser, HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'name',
        'normalized_name',
        'nik',
        'date_of_birth',
        'gender',
        'address',
        'parent_name',
        'phone',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date_of_birth' => 'date',
    ];

    /**
     * Get all measurements for this subject.
     */
    public function measurements(): HasMany
    {
        return $this->hasMany(Measurement::class);
    }

    /**
     * Get the latest measurement for this subject.
     */
    public function latestMeasurement()
    {
        return $this->hasOne(Measurement::class)->latestOfMany('measurement_date');
    }

    /**
     * Normalize the name for duplicate checking.
     * Per 06_data_dictionary.md normalization rules.
     */
    public static function normalizeName(string $name): string
    {
        return strtoupper(trim(preg_replace('/\s+/', ' ', $name)));
    }
}

