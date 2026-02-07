<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * SyncLog model for tracking offline sync batches
 */
class SyncLog extends Model
{
    protected $fillable = [
        'user_id',
        'batch_id',
        'total_records',
        'synced',
        'skipped',
        'conflicts',
        'status',
        'conflict_details',
    ];

    protected $casts = [
        'conflict_details' => 'array',
    ];

    /**
     * Get the user who initiated the sync.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mark sync as completed with results.
     */
    public function complete(int $synced, int $skipped, array $conflicts): self
    {
        $this->update([
            'synced' => $synced,
            'skipped' => $skipped,
            'conflicts' => count($conflicts),
            'conflict_details' => $conflicts,
            'status' => 'completed',
        ]);

        return $this;
    }

    /**
     * Mark sync as failed.
     */
    public function fail(string $reason): self
    {
        $this->update([
            'status' => 'failed',
            'conflict_details' => ['error' => $reason],
        ]);

        return $this;
    }
}
