<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Promotion extends Model
{
    use HasFactory, HasUuids;

    /**
     * The table associated with the model.
     */
    protected $table = 'promotions';

    /**
     * Position constants
     */
    const POSITION_TOP = 'top';
    const POSITION_BOTTOM = 'bottom';
    const POSITION_POPUP = 'popup';

    /**
     * Available positions
     */
    public static array $positions = [
        self::POSITION_TOP => 'Top Banner',
        self::POSITION_BOTTOM => 'Bottom Banner',
        self::POSITION_POPUP => 'Popup Modal',
    ];

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'title',
        'message',
        'button_text',
        'button_url',
        'image_path',
        'background_color',
        'background_color_2',
        'background_gradient_type',
        'background_gradient_direction',
        'text_color',
        'button_color',
        'button_color_2',
        'button_gradient_type',
        'button_gradient_direction',
        'button_text_color',
        'position',
        'start_date',
        'end_date',
        'is_active',
        'priority',
        'created_by',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'start_date' => 'datetime',
            'end_date' => 'datetime',
            'is_active' => 'boolean',
            'priority' => 'integer',
        ];
    }

    /**
     * Get the user who created this banner.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the CSS background style for the banner.
     */
    public function getBackgroundStyle(): string
    {
        if ($this->background_gradient_type === 'gradient' && $this->background_color_2) {
            $direction = $this->background_gradient_direction ?: 'to right';
            return "linear-gradient({$direction}, {$this->background_color}, {$this->background_color_2})";
        }
        return $this->background_color;
    }

    /**
     * Get the CSS background style for the button.
     */
    public function getButtonStyle(): string
    {
        if ($this->button_gradient_type === 'gradient' && $this->button_color_2) {
            $direction = $this->button_gradient_direction ?: 'to right';
            return "linear-gradient({$direction}, {$this->button_color}, {$this->button_color_2})";
        }
        return $this->button_color;
    }

    /**
     * Check if banner is currently active (enabled and within date range).
     */
    public function isCurrentlyActive(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $today = now()->startOfDay();
        
        // If no dates set, consider it always active when is_active is true
        if (!$this->start_date && !$this->end_date) {
            return true;
        }
        
        // Check start date if set (compare dates only, ignore time)
        if ($this->start_date && $today->lessThan($this->start_date->startOfDay())) {
            return false;
        }
        
        // Check end date if set (compare dates only, ignore time)
        if ($this->end_date && $today->greaterThan($this->end_date->startOfDay())) {
            return false;
        }
        
        return true;
    }

    /**
     * Check if banner is scheduled (hasn't started yet).
     */
    public function isScheduled(): bool
    {
        $today = now()->startOfDay();
        return $this->is_active && $this->start_date && $today->lessThan($this->start_date->startOfDay());
    }

    /**
     * Check if banner has expired.
     */
    public function isExpired(): bool
    {
        $today = now()->startOfDay();
        return $this->end_date && $today->greaterThan($this->end_date->startOfDay());
    }

    /**
     * Get the status of the banner.
     */
    public function getStatus(): string
    {
        if (!$this->is_active) {
            return 'inactive';
        }
        
        if ($this->isExpired()) {
            return 'expired';
        }
        
        if ($this->isScheduled()) {
            return 'scheduled';
        }
        
        return 'active';
    }

    /**
     * Get status display name.
     */
    public function getStatusDisplayName(): string
    {
        return match($this->getStatus()) {
            'active' => 'Active',
            'scheduled' => 'Scheduled',
            'expired' => 'Expired',
            'inactive' => 'Inactive',
            default => 'Unknown',
        };
    }

    /**
     * Get status badge class.
     */
    public function getStatusBadgeClass(): string
    {
        return match($this->getStatus()) {
            'active' => 'status-active',
            'scheduled' => 'status-scheduled',
            'expired' => 'status-expired',
            'inactive' => 'status-inactive',
            default => 'status-inactive',
        };
    }

    /**
     * Get position display name.
     */
    public function getPositionDisplayName(): string
    {
        return self::$positions[$this->position] ?? 'Unknown';
    }

    /**
     * Scope to get only currently active banners.
     */
    public function scopeCurrentlyActive($query)
    {
        $today = now()->startOfDay();
        return $query->where('is_active', true)
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today);
    }

    /**
     * Scope to get active banners by position.
     */
    public function scopeByPosition($query, string $position)
    {
        return $query->where('position', $position);
    }

    /**
     * Scope to order by priority.
     */
    public function scopeOrderByPriority($query)
    {
        return $query->orderBy('priority', 'desc');
    }

    /**
     * Get the full URL to the banner image.
     */
    public function getImageUrl(): ?string
    {
        return $this->image_path ? url($this->image_path) : null;
    }

    /**
     * Check if banner has an image.
     */
    public function hasImage(): bool
    {
        return !empty($this->image_path);
    }
}

