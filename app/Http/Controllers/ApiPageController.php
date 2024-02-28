<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ApiPageController extends Controller
{
    public function index(Request $request)
    {
        $pages = Page::latest();

        if ($request->keyword != '') {
            $pages = $pages->where('name', 'like', '%' . $request->keyword . '%');
        }

        $pages = $pages->paginate(10);

        return response()->json([
            'pages' => $pages,
        ]);
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'slug' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $page = new Page;
        $page->name = $request->name;
        $page->slug = $request->slug;
        $page->content = $request->content;
        $page->save();

        $message = 'Page added successfully.';

        return response()->json([
            'status' => true,
            'message' => $message,
        ]);
    }

    public function update($id, Request $request)
    {
        $page = Page::find($id);

        if ($page == null) {
            return response()->json([
                'status' => false,
                'message' => 'Page not found.',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'slug' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $page->name = $request->name;
        $page->slug = $request->slug;
        $page->content = $request->content;
        $page->save();

        $message = 'Page updated successfully.';

        return response()->json([
            'status' => true,
            'message' => $message,
        ]);
    }

    public function destroy($id)
    {
        $page = Page::find($id);

        if ($page == null) {
            return response()->json([
                'status' => false,
                'message' => 'Page not found.',
            ], 404);
        }

        $page->delete();

        $message = 'Page deleted successfully.';

        return response()->json([
            'status' => true,
            'message' => $message,
        ]);
    }
}
