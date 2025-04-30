<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateStoryRequest;
use App\Models\Story;
use App\Models\Tag;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ModeratorController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum', 'role:moderator']);
    }

    public function indexStories (): JsonResponse
    {
        $stories = Story::with('category', 'tags')->paginate(10);
        return response()->json(['data' => $stories]);
    }

    public function updateStory(UpdateStoryRequest $request, Story $story)
    {
        Gate::authorize('update', $story);

        $story->update($request->validated());

        $story->update($request->all());
        return response()->json(['message' => 'Story updated successfully', 'data' => $story]);
    }

    public function destroyStory(Story $story)
    {
        Gate::authorize('delete', $story);

        $story->delete();
        return response()->json(['message' => 'Story deleted successfully']);
    }

    public function indexPendingTags(Story $story)
    {
        $pendingTags = $story->pendingTags()->with('stories')->paginate();
        return response()->json(['data' => $pendingTags]);
    }

    public function approveTag(Story $story, Tag $tag)
    {
        $attached = $story->pendingTags()->where('tag_id', $tag->id)->first();
        if ($attached) {
            $story->pendingTags()->updateExistingPivot($tag->id, ['status' => 'approved']);
            return response()->json(['message' => "Tag '$tag->name' approved for story '$story->title'."]);
        }
        return response()->json(['message' => 'Tag is not pending for this story'], 404);
    }

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
