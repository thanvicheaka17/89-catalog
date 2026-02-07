<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;
class UserDevice extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'user_devices';

    protected $fillable = [
        'user_id',
        'device_name',
        'ip_address',
        'user_agent',
        'last_active_at',
        'revoked',
        'device_fingerprint',
    ];

    protected $casts = [
        'last_active_at' => 'datetime',
        'revoked' => 'boolean',
        'device_fingerprint' => 'string',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function isRevoked(): bool
    {
        return $this->revoked === true;
    }

    public function isNotRevoked(): bool
    {
        return $this->revoked === false;
    }

    public function getLastActiveAt(): ?Carbon
    {
        return $this->last_active_at;
    }

    public function getLastActiveAtFormatted(): string
    {
        return $this->last_active_at ? $this->last_active_at->format('d M Y H:i') : 'N/A';
    }

    public function getRevoked(): ?bool
    {
        return $this->revoked;
    }

    public function getRevokedFormatted(): string
    {
        return $this->getRevoked() ? 'Revoked' : 'Not Revoked';
    }
}
