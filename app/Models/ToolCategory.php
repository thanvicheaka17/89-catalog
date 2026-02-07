<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ToolCategory extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'tool_categories';

    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    /**
     * A category has many tools
     */
    public function tools()
    {
        return $this->hasMany(Tool::class, 'category_id');
    }
}
