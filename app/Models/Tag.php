<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * This model represents a tag within the application.
 * 
 * This model specifies all the fields that can be modified alongside all the
 * relationship instances associated with the 'tags' table, which includes
 * many-to-many relationships.
 */
class Tag extends Model
{
    use HasFactory;

    protected $fillable = [
        /**
         * The name of the tag that tells what to expect from the story
         * @var string
         */
        'name',
    ];

    /**
     * Get the stories that belong to the tag.
     * 
     * This method defines a many-to-many relationship between the Tag and Story models.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function stories() : BelongsToMany {
        return $this->belongsToMany(Story::class, 'stories_tags');
    }
}
