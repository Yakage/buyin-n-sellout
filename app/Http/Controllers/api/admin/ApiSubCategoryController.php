<?php

namespace App\Http\Controllers\api\admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class ApiSubCategoryController extends Controller
{
    public function index(Request $request){
        $subCategories = SubCategory::select('sub_categories.*','categories.name as categoryName')
                            ->latest('sub_categories.id')
                            ->leftJoin('categories','categories.id','sub_categories.category_id');

        if(!empty($request->get('keyword'))) {
            $subCategories = $subCategories->where('sub_categories.name', 'like', '%'.$request->get('keyword').'%')
                                            ->orWhere('categories.name', 'like', '%'.$request->get('keyword').'%');
        }

        $subCategories = $subCategories->paginate(10);

        return response()->json([
            'status' => true,
            'subCategories' => $subCategories
        ]);
    }

    public function create() {
        $categories = Category::orderBy('name','ASC')->get();

        return response()->json([
            'status' => true,
            'categories' => $categories
        ]);
    }

    public function store(Request $request) {
        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'slug' => 'required|unique:sub_categories',
            'category' => 'required',
            'status' => 'required'
        ]);

        if($validator->passes()) {

            $subCategory = new SubCategory();
            $subCategory->name = $request->name;
            $subCategory->slug = $request->slug;
            $subCategory->status = $request->status;
            $subCategory->category_id = $request->category;
            $subCategory->showHome = $request->showHome;
            $subCategory->save();

            return response()->json([
                'status' => true,
                'message' => 'Sub Category created successfully.'
            ]);

        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    public function edit($id, Request $request) {
        $subCategory = SubCategory::find($id);

        if (empty($subCategory)) {
            return response()->json([
                'status' => false,
                'message' => 'Record not found.'
            ]);
        }

        $categories = Category::orderBy('name','ASC')->get();

        return response()->json([
            'status' => true,
            'subCategory' => $subCategory,
            'categories' => $categories
        ]);
    }

    public function update($id, Request $request) {
        $subCategory = SubCategory::find($id);

        if(empty($subCategory)) {
            return response()->json([
                'status' => false,
                'message' => 'Record not found.'
            ]);
        }

        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'slug' => 'required|unique:sub_categories,slug,'.$subCategory->id.',id',
            'category' => 'required',
            'status' => 'required'
        ]);

        if($validator->passes()) {
            $subCategory->name = $request->name;
            $subCategory->slug = $request->slug;
            $subCategory->status = $request->status;
            $subCategory->category_id = $request->category;
            $subCategory->showHome = $request->showHome;
            $subCategory->save();

            return response()->json([
                'status' => true,
                'message' => 'Sub Category updated successfully.'
            ]);

        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    public function destroy($id, Request $request) {
        $subCategory = SubCategory::find($id);

        if(empty($subCategory)) {
            return response()->json([
                'status' => false,
                'message' => 'Record not found.'
            ]);
        }

        $subCategory->delete();

        return response()->json([
            'status' => true,
            'message' => 'Sub Category deleted successfully.'
        ]);
    }
}