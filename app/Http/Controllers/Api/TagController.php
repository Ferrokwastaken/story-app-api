<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * The TagController.
 * 
 * This class handles all the RESTful (GET, POST, PUT, DELETE, PATCH) operations by using
 * CRUD (Create, Read, Update, Delete) as the name of the methods. All of them return
 * their responses in JSON format, and if it's relevant, with an associated success or
 * error message.
 */
class TagController extends Controller
{
    /**
     * Display a listing of the tags.
     * 
     * This method fetches all the tags from the database using the 'Tag'
     * model. Then, in returns a JSON response containing a 'data' array
     * with the list of tags.
     * 
     * @return \Illuminate\Http\JsonResponse
     * Returns a JSON containing the relevant data
     */
    public function index() : JsonResponse
    {
        $tags = Tag::all();

        return response()->json([
            'data' => $tags,
        ]);
    }

    /**
     * Store a newly created tag in the database.
     * 
     * This method handles the creation of a new tag, validating the data
     * as it comes in. By using the 'Request' class for the HTTP request,
     * it can save a new tag that isn't already in the database. Finally, it
     * returns a JSON response, which could be either code 201 (success; new resource created) 
     * or 422 (User error; validation of data fails).
     *
     * @param \Illuminate\Http\Request $request
     * The HTTP instance to store a new category.
     * 
     * @return \Illuminate\Http\JsonResponse
     * Returns a JSON containing the relevant data.
     */
    public function store(Request $request) : JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:tags,name',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        $tag = Tag::create($request->all());

        return response()->json([
            'data' => $tag,
            'message' => 'Tag created successfully',
        ], 201);
    }

    /**
     * Display the specified tag. 
     * 
     * Retrieves and displays a specific tag, returning immediately as
     * a JSON with the data.
     * 
     * @param \App\Models\Tag $tag
     * The specified tag to see the contents of.
     * 
     * @return \Illuminate\Http\JsonResponse
     * Returns a JSON response with the relevant data
     */
    public function show(Tag $tag) : JsonResponse
    {
        return response()->json([
            'data' => $tag,
        ]);
    }

    /**
     * Update the specified resource in storage.
     * 
     * Handles the updating of a tag using the 'Request' class for the HTTP
     * request and the 'Tag' model to update the specified tag.
     * It validates the data, with the 'name' only being validated if it's changed.
     * Finally it returns a JSON with either a success message or an error code(422).
     * 
     * @param \Illuminate\Http\Request $request
     * The HTTP request to update the tag
     * @param \App\Models\Tag $tag
     * The 'Tag' model about to be updated
     * 
     * @return \Illuminate\Http\JsonResponse
     * Returns either with a success message or an error code.
     */
    public function update(Request $request, Tag $tag) : JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255|unique:tags,name,' . $tag->id,
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        $tag->update($request->all());

        return response()->json([
            'data' => $tag,
            'message' => 'Tag updated successfully',
        ]);
    }

    /**
     * Remove the specified tag from the database.
     * 
     * Deletes the specified tag from the database permanently.
     * If the tag is associated with one or more stories, it cannot be
     * deleted.
     * 
     * @param \App\Models\Tag $tag
     * The 'Tag' model instance about to be deleted
     * 
     * @return \Illuminate\Http\JsonResponse
     * Returns a JSON with either success or error (with the corresponding code) message.
     */
    public function destroy(Tag $tag) : JsonResponse
    {
        if ($tag->stories()->count() > 0) {
            return response()->json([
                'message' => 'Cannot delete tag with associated stories.',
            ], 409);
        }

        $tag->delete();

        return response()->json([
            'message' => 'Tag deleted successfully',
        ]);
    }
}
