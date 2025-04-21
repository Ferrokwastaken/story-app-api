<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * This model represents a comment within the application.
 * 
 * This model specifies all the fields that can be modified alongside all the
 * relationship instances associated with the 'stories' table, which includes
 * one-to-many relationships.
 */
class Comment extends Model
{
    use HasFactory;

    // These three variables are used to specify which of the fields is the primary key
    // and the type of data it stores. It also specifies that said unique ID is not auto-incrementing.
    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        /**
         * The unique, 36 character long ID of the comment
         * @var string
         */
        'uuid',
        /**
         * The UUID of the story that the comment is giving feedback on
         * @var string
         */
        'story_uuid',
        /**
         * The UUID of the user that made the comment
         * @var string
         */
        'user_uuid',
        /**
         * The content of the comment itself
         * @var string
         */
        'content',
        /**
         * The number of reports the comment has
         * @var int
         */
        'reports',
    ];

    /**
     * Get the story that the comment belongs to.
     * 
     * This method defines a one-to-many relationship between the Comment and Story models.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function story() : BelongsTo {
        return $this->belongsTo(Story::class);
    }

    /**
     * Get the reports that belong to the comment
     * 
     * This method defines a one-to-many relationship between a comment and its reports.
     * One comment can have many reports.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function reports() : HasMany {
        return $this->hasMany(CommentsReport::class);
    }
}
