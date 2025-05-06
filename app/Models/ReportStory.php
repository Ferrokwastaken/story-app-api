<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * This model represents a report to a story within the application.
 * 
 * This model specifies all the fields that can be modified alongside all the
 * relationship instances associated with the 'reports' table, which includes
 * one-to-many relationships.
 */
class ReportStory extends Model
{
    use HasFactory;

    protected $table = 'reports_stories';
    protected $fillable = [
        /**
         * The UUID of the story that the report is pointing to or being reported.
         * @var string
         */
        'story_uuid',
        /**
         * The UUID of the user that's making the report
         * @var string
         */
        'user_uuid',
        /**
         * The reason for the report.
         * @var string
         */
        'reason',
        /**
         * A written, optional field that further clarifies the reason
         * for the report
         * @var string
         */
        'details',
        /**
         * The status of the reports (pending, resolved)
         */
        'status',
    ];

    /**
     * Get the story that the report belongs to.
     * 
     * This method defines a one-to-many relationship between the Report and Story models.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function story() : BelongsTo {
        return $this->belongsTo(Story::class, 'story_uuid', 'uuid');
    }
}
