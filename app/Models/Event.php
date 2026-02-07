<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Event extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'events';

    protected $fillable = [
        'title',
        'description',
        'is_active',
        'start_at',
        'end_at',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isActive(): bool
    {
        return $this->is_active === true;
    }

    public function isInactive(): bool
    {
        return $this->is_active === false;
    }

    public function getStatus(): string
    {
        return $this->isActive() ? 'active' : 'inactive';
    }

    public function getStatusDisplayName(): string
    {
        return $this->getStatus() === 'active' ? 'Active' : 'Inactive';
    }

    public function getStatusBadgeClass(): string
    {
        return $this->getStatus() === 'active' ? 'status-active' : 'status-inactive';
    }
}
