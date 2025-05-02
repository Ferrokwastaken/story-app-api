<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Story;
use App\Models\Tag;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

/**
 * The StoryController
 * 
 * This class handles all the RESTful (GET, POST, PUT, DELETE, PATCH) operations by using
 * CRUD (Create, Read, Update, Delete) as the name of the methods. All of them return
 * their responses in JSON format, and if it's relevant, with an associated success or
 * error message.
 */
class StoryController extends Controller
{
    /**
     * Display a listing of the stories in JSON format.
     * 
     * This method fetches all the stories from the database using the Story model.
     * Then it eager loads the related categories and tags. The method can also
     * allow filtering, and finally returns a JSON response containing a 'data' array with the list of stories.
     * 
     * @return \Illuminate\Http\JsonResponse
     * Returns a JSON containing the relevant data
     */
    public function index(Request $request): JsonResponse
    {
        $query = Story::with('category', 'tags');

        if ($request->has('title')) { // Filter by title
            $searchTerm = $request->input('title');
            $query->where('title', 'like', '%' . $searchTerm . '%');
        }

        if ($request->has('category_id')) { // Filter by category
            $categoryId = $request->input('category_id');
            $query->where('category_id', $categoryId);
        }

        if ($request->has('start_date') && $request->has('end_date')) {
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        $stories = $query->paginate(10)->through(function ($story) {
            return [
                'uuid' => $story->uuid,
                'title' => $story->title,
                'description' => $story->description,
                'category' => $story->category,
                'created_at_formatted' => Carbon::parse($story->created_at)->format('d/m/Y H:i'),
            ];
        });

        return response()->json([
            'data' => $stories,
        ]);
    }

    /**
     * Store a newly created story in the database.
     * 
     * This method handles the creation of a new story, validating the data
     * as it comes in. Using the 'Request' class as the HTTP instance of the
     * 'Story' model it saves the new story. It checks if it also has tags using the tags() relationship
     * and the attach() method. Finally, it returns a JSON response.
     * 
     * @param \Illuminate\Http\Request $request
     * The HTTP instance to store a new story.
     * 
     * @return \Illuminate\Http\JsonResponse
     * Returns a JSON containing the relevant data
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'genre' => 'nullable|string|max:255',
            'length' => 'nullable|integer|min:0',
            'content' => 'nullable|string',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422); // 422: Unproccesable Entity status code 
        }

        $story = Story::create([
            'uuid' => Str::uuid(),
            'title' => $request->input('title'),
            'genre' => $request->input('genre'),
            'length' => $request->input('length'),
            'content' => $request->input('content'),
            'description' => $request->input('description'),
            'category_id' => $request->input('category_id'),
        ]);

        if ($request->has('tags')) {
            $story->tags()->attach($request->input('tags'));
        }

        return response()->json([
            'data' => $story,
            'message' => 'Story created successfully',
        ], 201);
    }

    /**
     * Display the specified story.
     * 
     * Retrieves and displays a specific story based on its UUID, then eager loads
     * the related categories, tags and comments. Finally, it returns a JSON containing
     * the story data.
     * 
     * @param \App\Models\Story $story
     * The specified story to see the contents of.
     * 
     * @return \Illuminate\Http\JsonResponse
     * Returns a JSON with the relevant data.
     */
    public function show(Story $story): JsonResponse
    {
        $story->load('category', 'tags', 'comments');
        return response()->json([
            'data' => $story,
        ]);
    }

    /**
     * Update the specified story in the database.
     * 
     * Handles the updating of a story by the use of the 'Request' class to
     * simulate the HTTP request for updating. It uses the $story model to update it.
     * If the request includes tags, it uses the sync() method to update the story's tags.
     * Finally it returns a JSON with the updated story.
     * 
     * @param \Illuminate\Http\Request $request
     * The HTTP request to update the story
     * @param \App\Models\Story $story
     * The 'Story' model about to be updated
     * 
     * @return \Illuminate\Http\JsonResponse
     * Returns a JSON with the updated story
     */
    public function update(Request $request, Story $story): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'genre' => 'nullable|string|max:255',
            'length' => 'nullable|integer|min:0',
            'content' => 'nullable|string',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        $story->update($request->all());

        if ($request->has('tags')) {
            $story->tags()->sync($request->input('tags'));
        }

        return response()->json([
            'data' => $story,
            'message' => 'Story updated successfully',
        ]);
    }

    /**
     * Remove the specified story from the database.
     * 
     * Deletes the specified story from the database permanently.
     * 
     * @param \App\Models\Story $story
     * The 'Story' model instance about to be deleted.
     * 
     * @return \Illuminate\Http\JsonResponse
     * Returns a JSON with a success message.
     */
    public function destroy(Story $story): JsonResponse
    {
        $story->delete();

        return response()->json([
            'message' => 'Story deleted successfully',
        ]);
    }

    /**
     * Handles the reporting process of a specific story.
     * 
     * Validating the data before submitting the form, this method logs
     * a report made to a story with the reason, details and the user's uuid.
     * Then, it returns a JSON with a success message.
     * 
     * @param \Illuminate\Http\Request $request
     * The HTTP request of the story about to be reported.
     * @param \App\Models\Story $story
     * The specific story about to be reported.
     * 
     * @return \Illuminate\Http\JsonResponse
     * Returns a JSON with a success message.
     */
    public function report(Request $request, Story $story): JsonResponse
    {
        $request->validate([
            'reason' => 'required|string|max:255',
            'details' => 'nullable|string',
            'user_uuid' => 'required|uuid',
        ]);

        $story->reports()->create([
            'user_uuid' => $request->input('user_uuid'),
            'reason' => $request->input('reason'),
            'details' => $request->input('details'),
        ]);

        return response()->json([
            'message' => 'Story reported successfully',
        ], 201);
    }

    /**
     * Handles the associating of tags to a story.
     * 
     * This method validates the tags that should be added to a story,
     * which then is added to said story model.
     * 
     * @param \Illuminate\Http\Request $request
     * The HTTP request of the story that the tags are added into.
     * @param \App\Models\Story $story
     * The story model about to have tags added.
     * 
     * @return \Illuminate\Http\JsonResponse
     * Returns a JSON with either a success or error code plus a message.
     */
    public function addTag(Request $request, Story $story): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'tag_id' => 'required|exists:tags,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $tagId = $request->input('tag_id');

        if (
            !$story->tags()->where('tag_id', $tagId)->wherePivot('status', 'approved')->exists() &&
            !$story->pendingTags()->where('tag_id', $tagId)->exists()
        ) {
            $story->pendingTags()->attach($tagId, ['status' => 'pending']);
            return response()->json(['message' => "Tag addition request submitted for moderation."], 200);
        } else {
            return response()->json(['message' => "Tag is already attached or pending."], 200);
        }
    }
}
