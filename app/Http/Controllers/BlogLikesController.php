<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BlogLikes;
use App\Models\Blog;
use Illuminate\Support\Facades\Auth;

class BlogLikesController extends Controller
{
    public function toggle($blogId)
    {

        try {
            //code...

            $user = Auth::user();
            if ($user === null) {
                return response()->json([
                    'error' => 'yup'
                ]);
            }

            $blog = Blog::findOrFail($blogId);

            $like = BlogLikes::where('user_id', $user->id)
                ->where('blog_id', $blog->id)
                ->first();

            if ($like) {
                $like->delete();
                return response()->json([
                    'liked' => false,
                ]);
            } else {
                BlogLikes::create([
                    'user_id' => $user->id,
                    'blog_id' => $blog->id,
                ]);
                return response()->json([
                    'liked' => true,
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'error' => 'Something went wrong!',
                'message' => $th->getMessage(),
            ], 500);
        }
    }
}