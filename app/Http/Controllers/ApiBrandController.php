<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ApiBrandController extends Controller
{
    public function index(Request $request) {
        $brands = Brand::latest();

        if ($request->get('keyword')) {
            $brands = $brands->where('name', 'like','%'.$request->keyword.'%');
        }

        $brands = $brands->paginate(10);

        return response()->json(['brands' => $brands]);
    }

    public function create() {
        // For an API, you might not need to return a view for creating a brand.
        // You can either return a JSON response indicating how to create a brand or handle brand creation via another API endpoint.
    }

    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'slug' => 'required|unique:brands',
        ]);

        if ($validator->passes()) {
            $brand = new Brand();
            $brand->name = $request->name;
            $brand->slug = $request->slug;
            $brand->status = $request->status;
            $brand->save();

            return response()->json(['message' => 'Brand added successfully'], 201);
        } else {
            return response()->json(['errors' => $validator->errors()], 422);
        }
    }

    public function edit($id) {
        $brand = Brand::find($id);

        if (empty($brand)) {
            return response()->json(['error' => 'Brand not found'], 404);
        }

        return response()->json(['brand' => $brand]);
    }

    public function update($id, Request $request) {
        $brand = Brand::find($id);

        if (empty($brand)) {
            return response()->json(['error' => 'Brand not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'slug' => 'required|unique:brands,slug,'.$brand->id.',id',
        ]);

        if ($validator->passes()) {
            $brand->name = $request->name;
            $brand->slug = $request->slug;
            $brand->status = $request->status;
            $brand->save();

            return response()->json(['message' => 'Brand updated successfully']);
        } else {
            return response()->json(['errors' => $validator->errors()], 422);
        }
    }
}
