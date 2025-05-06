<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBlogRequest;
use App\Http\Requests\UpdateBlogRequest;
use App\Http\Resources\BlogCollection;
use App\Http\Resources\BlogResource;
use App\Models\Blog;
use Illuminate\Http\Request;
class BlogController extends Controller
{
    public function index()
    {
        try {

            $blogs = Blog::paginate(10);
            return new BlogCollection($blogs);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => 'Something went wrong!',
                'message' => $th->getMessage()
            ], 500);
        }

    }


    public function show($slug)
    {
        try {
            $blog = Blog::where('slug', $slug)->first();
            return new BlogResource($blog);
            // return new BlogResource(Blog::findOrFail($slug));
        } catch (\Throwable $th) {
            return response()->json([
                'error' => 'Something went wrong!',
                'message' => $th->getMessage()
            ], 500);
        }
    }



    public function store(StoreBlogRequest $request)
    {
        try {
            $validatedData = $request->validated();

            if ($request->hasFile('img')) {
                $img = $request->file('img');
                $imgName = time() . '_' . $img->getClientOriginalName();
                $imgPath = $img->storeAs('blogs', $imgName, 'public');
                $validatedData['img'] = $imgPath;
            }

            $blog = Blog::create($validatedData);

            return (new BlogResource($blog))
                ->response()
                ->setStatusCode(201);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => 'Something went wrong!',
                'message' => $th->getMessage()
            ], 500);
        }
    }


    public function update(UpdateBlogRequest $request, $id)
    {
        $blog = Blog::findOrFail($id);
        $validatedData = $request->validated();
        $blog->update($validatedData);
        return response()->json([
            'message' => 'Blog updated successfully',
            'data' => new BlogResource($blog)
        ]);
    }


    public function departmentBlogs($id)
    {
        try {
            $blogs = Blog::where('department_id', $id)->paginate(10);
            if (!$blogs) {
                return response()->json([
                    'message' => 'blogs not found'
                ]);
            }
            return new BlogCollection($blogs);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => 'something went wrong!',
                'message' => $th->getMessage()
            ]);

        }
    }


    public function search($keyword)
    {

        $blogs = Blog::limit(10)->select(
            [
                "img",
                "title",
                "id"
            ]
        )->where('title', 'like', '%' . $keyword . '%')->get();

        if (!$blogs) {
            return response()->json([
                'message' => 'blogs not found'
            ]);

        }

        return response()->json([
            'blog posts' => $blogs,

        ], 200);
    }

    public function destroy($id)
    {
        try {

            $blog = Blog::findOrFail($id);

            if (!$blog) {
                return response()->json([
                    'message' => 'Blog is not found'
                ]);
            }

            $blog->delete();
            return response()->json([
                'message' => 'Blog deleted successfully'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => 'something went wrong!',
                'message' => $th->getMessage()
            ]);
        }
    }

}
