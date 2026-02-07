<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Game extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'games';

    protected $fillable = [
        'name',
        'provider_id',
        'slug',
        'description',
        'image_path',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
        'image_path' => 'string',
        'slug' => 'string',
        'description' => 'string',
        'name' => 'string',
        'provider_id' => 'string',
    ];

    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class);
    }

    public function getImageUrl(): string
    {
        return $this->image_path ? url($this->image_path) : null;
    }

    public function hasImage(): bool
    {
        return !empty($this->image_path);
    }
}
