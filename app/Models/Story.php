<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * This model represents a story within the application.
 * 
 * This model specifies all the fields that can be modified alongside all the
 * relationship instances associated with the 'stories' table, which includes
 * one-to-one and many-to-one relationships.
 */
class Story extends Model
{
    use HasFactory;

    // Indicates what column is the primary key, which type of data it holds
    // and makes sure it doesn't auto increment.
    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        /**
         * The unique, 36 character long ID of the story
         * @var string
         */
        'uuid',
        /**
         * The title of the story
         * @var string
         */
        'title',
        /**
         * The type of genre the story proclaims to be
         * @var string
         */
        'genre',
        /**
         * The length of the story in number of words
         * @var int
         */
        'length',
        /**
         * The content of the story itself
         * @var string
         */
        'content',
        /**
         * The description of the story itself
         * @var string
         */
        'description',
        /**
         * The ID of the category that the story belongs to
         * @var int
         */
        'category_id',
    ];

    /**
     * Get the category that the story belongs to.
     * 
     * This method defines a one-to-many relationship between the Story and Category models.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the comments giving feedback on the story.
     * 
     * This method defines a one-to-many relationship between the Story and Comment models.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Get the reports of inappropriate content of the story.
     * 
     * This method defines a one-to-many relationship between the Story and Report models.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function reports(): HasMany
    {
        return $this->hasMany(ReportStory::class);
    }

    /**
     * Get the tags that are pending approval for the story.
     *
     * Defines a many-to-many relationship between a Story and Tags, filtered to include only
     * tags where the 'status' in the pivot table ('stories_tags') is 'pending'.
     * You can access these pending tags using `$story->pendingTags`.
     *
     * @return BelongsToMany
     */
    public function pendingTags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'stories_tags', 'story_uuid', 'tag_id', 'uuid')
            ->wherePivot('status', 'pending');
    }

    /**
     * Get the tags that have been approved for the story.
     *
     * Defines a many-to-many relationship between a Story and Tags, filtered to include only
     * tags where the 'status' in the pivot table ('stories_tags') is 'approved'.
     * You can access these approved tags using `$story->approvedTags`.
     *
     * @return BelongsToMany
     */
    public function approvedTags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'stories_tags', 'story_uuid', 'tag_id', 'uuid')
            ->wherePivot('status', 'approved');
    }

    /**
     * Get all approved tags associated with the story.
     *
     * This is a convenience method that simply returns the result of the `approvedTags()` relationship.
     * It allows you to access the story's approved tags using `$story->tags`.
     *
     * @return BelongsToMany
     */
    public function tags(): BelongsToMany
    {
        return $this->approvedTags();
    }

    public function ratings(): HasMany
    {
        return $this->hasMany(StoryRating::class, 'story_uuid', 'uuid');
    }

    public function averageRating()
    {
        return $this->ratings()->avg('rating');
    }
}
