<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Category;
use App\Models\TempImage;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;

class ApiCategoryController extends Controller
{
    public function index(Request $request) {
        $categories = Category::latest();

        if(!empty($request->get('keyword'))) {
            $categories = $categories->where('name', 'like', '%'.$request->get('keyword').'%');
        }

        $categories = $categories->paginate(10);

        return response()->json(['categories' => $categories]);
    }

    public function create(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'slug' => 'required|unique:categories',
        ]);

        if($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $category = new Category();
        $category->name = $request->name;
        $category->slug = $request->slug;
        $category->status = $request->status;
        $category->showHone = $request->showHome;
        $category->save();

        // Handle image saving here if needed

        return response()->json(['message' => 'Category created successfully'], 201);
    }

    public function edit($categoryId) {
        $category = Category::find($categoryId);

        if(empty($category)) {
            return response()->json(['error' => 'Category not found'], 404);
        }

        return response()->json(['category' => $category]);
    }

    public function update($categoryId, Request $request) {
        $category = Category::find($categoryId);

        if(empty($category)) {
            return response()->json(['error' => 'Category not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'slug' => 'required|unique:categories,slug,'.$category->id,
        ]);

        if($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $category->name = $request->name;
        $category->slug = $request->slug;
        $category->status = $request->status;
        $category->showHone = $request->showHome;
        $category->save();

        // Handle image updating here if needed

        return response()->json(['message' => 'Category updated successfully']);
    }

    public function destroy($categoryId) {
        $category = Category::find($categoryId);

        if(empty($category)) {
            return response()->json(['error' => 'Category not found'], 404);
        }

        // Handle deletion of related resources like images

        $category->delete();

        return response()->json(['message' => 'Category deleted successfully']);
    }
}
