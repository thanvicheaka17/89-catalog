<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class PremiumTool extends Model
{
    use HasFactory, HasUuids;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'title',
        'description',
        'is_active',
        'created_by',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the status of the tool.
     */
    public function getStatus(): string
    {
        return $this->is_active ? 'active' : 'inactive';
    }

    /**
     * Get the status display name.
     */
    public function getStatusDisplayName(): string
    {
        return $this->getStatus() === 'active' ? 'Active' : 'Inactive';
    }

    /**
     * Get the status badge class.
     */
    public function getStatusBadgeClass(): string
    {
        return $this->getStatus() === 'active' ? 'status-active' : 'status-inactive';
    }
}
