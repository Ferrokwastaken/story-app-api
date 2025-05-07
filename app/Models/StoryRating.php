<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StoryRating extends Model
{
    use HasFactory;

    protected $fillable = [
        'story_uuid',
        'rating',
    ];

    public function story(): BelongsTo
    {
        return $this->belongsTo(Story::class, 'story_uuid', 'uuid');
    }
}
