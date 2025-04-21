<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * The CategoryController.
 * 
 * This class handles all the RESTful (GET, POST, PUT, DELETE, PATCH) operations by using
 * CRUD (Create, Read, Update, Delete) as the name of the methods. All of them return
 * their responses in JSON format, and if it's relevant, with an associated success or
 * error message.
 */
class CategoryController extends Controller
{
    /**
     * Display a listing of categories.
     * 
     * This method fetches all the categories from the database using the
     * 'Category' model. Then it returns a JSON response containing a 'data'
     * array with the list of categories.
     * 
     * @return \Illuminate\Http\JsonResponse
     * Returns a JSON containing the relevant data
     */
    public function index() : JsonResponse
    {
        $categories = Category::all();

        return response()->json([
            'data' => $categories,
        ]);
    }

    /**
     * Store a newly created category in the database.
     * 
     * This method handles the creation of a new category, validating the data
     * as it comes in. By using the 'Request' class for the HTTP request,
     * it can save a new category that isn't already in the database. Finally, it
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
            'name' => 'required|string|max:255|unique:categories,name',
            'genre' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        $category = Category::create($request->all());

        return response()->json([
            'data' => $category,
            'message' => 'Category successfully created',
        ], 201);
    }

    /**
     * Display the specified category.
     * 
     * Retrieves and displays a specific category, returning immediately as
     * a JSON with the data.
     * 
     * @param \App\Models\Category $category
     * The specified category to see the contents of.
     * 
     * @return \Illuminate\Http\JsonResponse
     * Returns a JSON response with the relevant data
     */
    public function show(Category $category) : JsonResponse
    {
        return response()->json([
            'data' => $category,
        ]);
    }

    /**
     * Update the specified category in the database.
     * 
     * Handles the updating of a category using the 'Request' class for the HTTP
     * request and the 'Category' model to update the specified category.
     * It validates the data, with the 'name' only being validated if it's changed.
     * Finally it returns a JSON with either a success message or an error code(422).
     * 
     * @param \Illuminate\Http\Request $request
     * The HTTP request to update the category
     * @param \App\Models\Category $category
     * The 'Category' model about to be updated
     * 
     * @return \Illuminate\Http\JsonResponse
     * Returns either with a success message or an error code.
     */
    public function update(Request $request, Category $category) : JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255|unique:categories,name,' . $category->id,
            'genre' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        $category->update($request->all());

        return response()->json([
            'data' => $category,
            'message' => 'Category updated successfully',
        ]);
    }

    /**
     * Remove the specified category from the database.
     * 
     * Deletes the specified category from the database permanently.
     * If the category is associated with one or more stories, it cannot be
     * deleted.
     * 
     * @param \App\Models\Category $category
     * The 'Category' model instance about to be deleted
     * 
     * @return \Illuminate\Http\JsonResponse
     * Returns a JSON with either success or error (with the corresponding code) message.
     */
    public function destroy(Category $category) : JsonResponse
    {
        if ($category->stories()->count() > 0) {
            return response()->json([
                'message' => 'Cannot delete a category with associated stories.',
            ], 409);
        }

        $category->delete();

        return response()->json([
            'message' => 'Category deleted successfully',
        ]);
    }
}
