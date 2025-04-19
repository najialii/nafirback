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
        $blogs = Blog::paginate(10);
        return new BlogCollection($blogs);
    }

    public function show($id)
    {
        return new BlogResource(Blog::findOrFail($id));
    }


    
    public function store(StoreBlogRequest $request)
    {
        $validatedData = $request->validated();
        $blog = Blog::create($validatedData);
        return new BlogResource($blog);
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

    public function destroy($id)
    {
        $blog = Blog::findOrFail($id);
        $blog->delete();
        return response()->json([
            'message' => 'Blog deleted successfully'
        ]);
    }

}
