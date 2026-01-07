<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'color',
    ];

    /**
     * Get posts with this tag.
     */
    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class)->withTimestamps();
    }

    /**
     * Get CSS class for tag color.
     */
    public function getColorClassAttribute(): string
    {
        $colors = [
            'blue' => 'bg-blue-100 text-blue-700',
            'green' => 'bg-green-100 text-green-700',
            'red' => 'bg-red-100 text-red-700',
            'yellow' => 'bg-yellow-100 text-yellow-700',
            'purple' => 'bg-purple-100 text-purple-700',
            'pink' => 'bg-pink-100 text-pink-700',
            'indigo' => 'bg-indigo-100 text-indigo-700',
            'gray' => 'bg-gray-100 text-gray-600',
        ];

        return $colors[$this->color] ?? $colors['gray'];
    }
}
