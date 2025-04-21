<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Story;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CommentController extends Controller
{
    /**
     * Display a listing of the comments for a specific story.
     * 
     * This method fetches the comments nested inside a story using that
     * specific story's model. Then, it returns the data as a JSON.
     * 
     * @param \App\Models\Story $story
     * The 'Story' model in which the comments are nestled in.
     * 
     * @return \Illuminate\Http\JsonResponse
     * Returns a JSON response with the corresponding data.
     */
    public function index(Story $story) : JsonResponse 
    {
        $comments = $story->comments()->get();

        return response()->json([
            'data' => $comments,
        ]);
    }

    /**
     * Store a newly created comment for a specific story in the database.
     * 
     * This method validates the input, and then creates a new comment associated
     * with the specified $story model. It then returns either a success or error message with
     * their corresponding codes (201 for success; 422 for error).
     * 
     * @param \Illuminate\Http\Request $request
     * The HTTP request for storing the new comment.
     * @param \App\Models\Story $story
     * The 'Story' model that the comment is associated to.
     * 
     * @return \Illuminate\Http\JsonResponse
     * Returns a JSON response indicating if it was either a success or failure.
     */
    public function store(Request $request, Story $story) : JsonResponse 
    {
        $validator = Validator::make($request->all(), [
            'content' => 'required|string|max:1000',
            'user_uuid' => 'required|uuid',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        $comment = $story->comments()->create([
            'uuid' => Str::uuid(),
            'content' => $request->input('content'),
            'user_uuid' => $request->input('user_uuid'),
        ]);

        return response()->json([
            'data' => $comment,
            'message' => 'Comment created successfully',
        ], 201);
    }

    /**
     * Display the specified comment.
     * 
     * This method fetches the selected comment and shows it, by using
     * the $comment model. Then, it returns a JSON response showing
     * the relevant data.
     * 
     * @param \App\Models\Comment $comment
     * The Comment Model that's shown in detail.
     * 
     * @return \Illuminate\Http\JsonResponse
     * Returns a JSON response with the specified data.
     */
    public function show(Comment $comment) : JsonResponse 
    {
        return response()->json([
            'data' => $comment,
        ]);
    }

    /**
     * Update the specific comment under a specific story in the database.
     * 
     * This method validates the input data of the comment, and updates it
     * accordingly or returns an error if it doesn't match the validation.
     * 
     * @param \Illuminate\Http\Request $request
     * The HTTP request to update the comment.
     * @param \App\Models\Comment $comment
     * The $comment model instance about to be updated.
     * 
     * @return \Illuminate\Http\JsonResponse
     * Returns a JSON respons, either a success or error messages.
     */
    public function update(Request $request, Comment $comment) : JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'content' => 'sometimes|required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        $comment->update($request->all());

        return response()->json([
            'data' => $comment,
            'message' => 'Comment updated successfully',
        ]);
    }

    public function destroy(Comment $comment) : JsonResponse
    {
        $comment->delete();

        return response()->json([
            'message' => 'Comment deleted successfully',
        ]);
    }

    public function report(Request $request, Comment $comment)
    {
        $validator = Validator::make($request->all(), [
            'reason' => 'required|string|max:255',
            'details' => 'nullable|string',
            'user_uuid' => 'required|uuid',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        $comment->reports()->create([
            'reason' => $request->input('reason'),
            'details' => $request->input('details'),
        ]);

        $comment->increment('reports');

        return response()->json([
            'message' => 'Comment reported successfully',
        ], 201);
    }
}
