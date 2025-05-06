<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateStoryRequest;
use App\Models\CommentsReport;
use App\Models\ReportStory;
use App\Models\Story;
use App\Models\Tag;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

/**
 * The ModeratorController
 * 
 * This class handles everything related to the tasks moderators
 * have to do. Their main function is to approve/reject tags, and take
 * care of reports.
 */
class ModeratorController extends Controller
{
    /**
     * Constructor for the ModeratorController.
     * 
     * It applies the 'auth:sanctum' middleware to ensure only authenticated users can access these routes
     * an the 'role:moderator' middleware to ensure only users with the 'moderator' role can proceed.
     */
    public function __construct()
    {
        $this->middleware(['auth:sanctum', 'role:moderator']);
    }

    /**
     * The home method for the moderator's dashboard.
     * 
     * This method loads the pending actions left for moderators to do,
     * counting them for display on the frontend accordingly.
     * 
     * @return \Illuminate\Http\JsonResponse
     * Returns a JSON response containing all the pending actions.
     */
    public function home(): JsonResponse
    {
        $pendingTagCount = Tag::whereHas('stories', function ($query) {
            $query->where('stories_tags.status', 'pending');
        })->count();

        $storyReportCount = ReportStory::count();
        $commentReportCount = CommentsReport::count();

        return response()->json([
            'pendingTagCount' => $pendingTagCount,
            'storyReportCount' => $storyReportCount,
            'commentReportCount' => $commentReportCount,
        ]);
    }

    /**
     * Display a paginated list of all stories.
     * 
     * This method retrieves all stories from the database, eager loads their associated
     * categories and tags to reduce database queries, and then paginates the results.
     * It also logs the user who called this method for auditing purposes.
     * 
     * @param \Illuminate\Http\Request $request
     * The incoming HTTP request
     * 
     * @return \Illuminate\Http\JsonResponse
     * Returns a JSON response containing a 'data' array with the paginated list of stories.
     */
    public function indexStories(Request $request): JsonResponse
    {
        Log::info('indexStories called by user: ', ['user' => $request->user()]);
        $stories = Story::with('category', 'tags')->paginate(10);
        return response()->json(['data' => $stories]);
    }

    /**
     * Update an existing story.
     * 
     * This method first authorizes the user to update the given story using Laravel's Gate.
     * If authorized, it validates the incoming request data using the UpdateStoryRequest.
     * Then, it updates the story's attributes with the validated data.
     * Finally, it returns a JSON response indicating success and including the updated story data.
     * 
     * @param App\Http\Requests\UpdateStoryRequest $request
     * The HTTP request containing the updated story data.
     * @param \App\Models\Story $story
     * The Story model instance about to be updated.
     * 
     * @return \Illuminate\Http\JsonResponse
     * Returns a JSON response with a success message
     */
    public function updateStory(UpdateStoryRequest $request, Story $story): JsonResponse
    {
        Gate::authorize('update', $story);

        $story->update($request->validated());

        return response()->json(['message' => 'Story updated successfully', 'data' => $story]);
    }

    /**
     * Delete an existing story
     * 
     * This method authorizes the user to delete the give story using Laravel's Gate.
     * If authorized, it deletes the story from the database.
     * Finally, it returns a JSON response indicating successful deletion.
     * 
     * @param \App\Models\Story $story
     * The Story model instance about to be deleted.
     * 
     * @return \Illuminate\Http\JsonResponse
     * Returns a JSON response with a success message.
     */
    public function destroyStory(Story $story): JsonResponse
    {
        Gate::authorize('delete', $story);

        $story->delete();
        return response()->json(['message' => 'Story deleted successfully']);
    }

    /**
     * Display a paginated list of pending tags for a specific story.
     * 
     * This method retrieves the pending tags associated with a given story.
     * It uses the 'pendingTags' relationship defined in the Story model and eager loads
     * the 'stories' relationship for each pending tag. The results are then paginated.
     * 
     * @param \App\Models\Story $story
     * The Story model instance whose pending tags are to be retrieved.
     * 
     * @return \Illuminate\Http\JsonResponse
     * Returns a JSON response containing a 'data' array with the paginated
     * list of pending tags.
     */
    public function indexPendingTags(Story $story): JsonResponse
    {
        $pendingTags = $story->pendingTags()->with('stories')->paginate();
        return response()->json(['data' => $pendingTags]);
    }

    /**
     * Approve a pending tag for a specific story.
     *
     * This method checks if a given tag is currently pending for a specific story.
     * If it is, it updates the pivot table entry for that tag and story, setting the 'status' to 'approved'.
     * Finally, it returns a JSON response indicating the tag has been approved.
     * If the tag is not pending for the story, it returns a 404 error.
     *
     * @param App\Models\Story $story 
     * The Story model instance for which the tag is being approved (resolved by route model binding).
     * @param App\Models\Tag $tag 
     * The Tag model instance to be approved (resolved by route model binding).
     * 
     * @return Illuminate\Http\JsonResponse 
     * Returns a JSON response with a success message or a 404 error if the tag is not pending.
     */
    public function approveTag(Story $story, Tag $tag): JsonResponse
    {
        $attached = $story->pendingTags()->where('tag_id', $tag->id)->first();
        if ($attached) {
            $story->pendingTags()->updateExistingPivot($tag->id, ['status' => 'approved']);
            return response()->json(['message' => "Tag '$tag->name' approved for story '$story->title'."]);
        }
        return response()->json(['message' => 'Tag is not pending for this story'], 404);
    }

    /**
     * Reject a pending tag for a specific story.
     *
     * This method checks if a given tag is currently pending for a specific story.
     * If it is, it detaches the tag from the story's pending tags relationship, effectively rejecting it.
     * Finally, it returns a JSON response indicating the tag has been rejected.
     * If the tag is not pending for the story, it returns a 404 error.
     *
     * @param App\Models\Story $story 
     * The Story model instance for which the tag is being rejected (resolved by route model binding).
     * @param App\Models\Tag $tag 
     * The Tag model instance to be rejected (resolved by route model binding).
     * 
     * @return Illuminate\Http\JsonResponse Returns a JSON response with a success message or a 404 error if the tag is not pending.
     */
    public function rejectTag(Story $story, Tag $tag)
    {
        $attached = $story->pendingTags()->where('tag_id', $tag->id)->first();
        if ($attached) {
            $story->pendingTags()->detach($tag->id);
            return response()->json(['message' => "Tag '$tag->name' rejected for story '$story->title'."]);
        }
        return response()->json(['message' => 'Tag is not pending for this story'], 404);
    }
}
