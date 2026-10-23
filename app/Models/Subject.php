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
        'card_token',
        'name',
        'normalized_name',
        'date_of_birth',
        'gender',
        'address',
        'parent_name',
        'phone',
        'pregnancy_start_date',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date_of_birth' => 'date',
        'pregnancy_start_date' => 'date',
    ];

    protected static function booted(): void
    {
        static::creating(function (Subject $subject) {
            if (empty($subject->card_token)) {
                $subject->card_token = (string) \Illuminate\Support\Str::uuid();
            }
        });
    }

    /**
     * Get or create a secure unguessable card token.
     */
    public function getSecureCardToken(): string
    {
        if (!empty($this->card_token)) {
            return $this->card_token;
        }

        $token = (string) \Illuminate\Support\Str::uuid();
        $this->updateQuietly(['card_token' => $token]);

        return $token;
    }


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

