<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class CasinoCategory extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'casino_categories';

    protected $fillable = [
        'slug',
        'name',
        'description',
        'logo',
    ];

    /**
     * Get the casinos for this category
     */
    public function casinos(): HasMany
    {
        return $this->hasMany(Casino::class);
    }

    /**
     * Get category by slug
     */
    public static function findBySlug(string $slug): ?self
    {
        return static::where('slug', $slug)->first();
    }

    public function hasLogo(): bool
    {
        return !empty($this->logo);
    }

    public function getLogoUrl(): string
    {
        return $this->logo ? url($this->logo) : null;
    }

    public function getCasinoCount(): int
    {
        return $this->casinos()->count();
    }
}
