<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * This model represents the reports of a comment in the application
 * 
 * This model specifies all the fields that can be modified alongside all the relationship
 * instance associated with the 'comments' table, which in this case is many-to-one.
 */
class CommentsReport extends Model
{
    use HasFactory;

    protected $fillable = [
        /**
         * The unique, 36 character long ID of the comment that
         * the report is associated to
         * @var string
         */
        'comment_uuid',
        /**
         * The reason why the reports was made.
         * @var string
         */
        'reason',
        /**
         * An optional field written by the user that further clarifies
         * the reason for the report
         * @var string
         */
        'details',
    ];

    /**
     * Get the comment that the reports are assigned to.
     * 
     * This method defines a many-to-one relationship between the reports and the comments.
     * Many reports belong to one comments.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function comment() : BelongsTo {
        return $this->belongsTo(Comment::class, 'comment_uuid', 'uuid');
    }
}
