<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
class SiteSetting extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'site_settings';

    protected $fillable = [
        'key',
        'value',
        'group',
    ];

    protected $casts = [
        'value' => 'array',
    ];

    public function getGroupDisplayName(): string
    {
        return match($this->group) {
            'general' => 'General',
            'contact' => 'Contact',
            'social' => 'Social Media',
            'analytics' => 'Analytics',
            'seo' => 'SEO',
            'footer' => 'Footer',
            'global' => 'Global',
            'tools' => 'Tools',
            'other' => 'Other',
            default => ucfirst($this->group ?? 'Unknown'),
        };
    }

    public function getGroupBadgeClass(): string
    {
        return match($this->group) {
            'general' => 'blue',
            'contact' => 'indigo',
            'social' => 'indigo',
            'analytics' => 'yellow',
            'seo' => 'red',
            'footer' => 'gray',
            'global' => 'blue',
            'tools' => 'blue',
            default => 'secondary',
        };
    }

    public function getValueDisplayName(): string
    {
        if (is_array($this->value)) {
            return 'Array data (' . count($this->value) . ' items)';
        }
        return Str::limit($this->value ?? '', 50, '...');
    }

    /**
     * Get a site setting value by key
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get(string $key, $default = null)
    {
        $setting = static::where('key', $key)->first();

        return $setting ? $setting->value : $default;
    }

    /**
     * Set a site setting value
     *
     * @param string $key
     * @param mixed $value
     * @param string $group
     * @return static
     */
    public static function set(string $key, $value, string $group = 'general'): static
    {
        return static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'group' => $group,
            ]
        );
    }

    /**
     * Get all settings for a specific group
     *
     * @param string $group
     * @return \Illuminate\Support\Collection
     */
    public static function getGroup(string $group): \Illuminate\Support\Collection
    {
        return static::where('group', $group)->get()->pluck('value', 'key');
    }
}
