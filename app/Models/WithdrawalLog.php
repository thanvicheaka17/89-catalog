<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class WithdrawalLog extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'rtp_game_id',
        'user_identifier',
        'withdrawal_amount',
        'transfer_proof_url',
        'status',
        'is_broadcasted',
        'withdrawal_date',
    ];

    protected $casts = [
        'withdrawal_amount' => 'decimal:2',
        'is_broadcasted' => 'boolean',
        'withdrawal_date' => 'datetime',
    ];

    /**
     * Get the RTP game associated with this withdrawal (if any)
     */
    public function rtpGame(): BelongsTo
    {
        return $this->belongsTo(RTPGame::class, 'rtp_game_id');
    }

    /**
     * Scope for successful withdrawals
     */
    public function scopeSuccessful($query)
    {
        return $query->where('status', 'successful');
    }

    /**
     * Scope for broadcasted withdrawals
     */
    public function scopeBroadcasted($query)
    {
        return $query->where('is_broadcasted', true);
    }

    /**
     * Scope for pending broadcasts
     */
    public function scopePendingBroadcast($query)
    {
        return $query->where('status', 'successful')
                    ->where('is_broadcasted', false);
    }
}
