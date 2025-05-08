<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BlogLikes;
use App\Models\Blog;
use Illuminate\Support\Facades\Auth;

class BlogLikesController extends Controller
{
    public function toggle(Request $request, $blogId)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['error' => 'Unauthenticated'], 401);
            }

            $request->validate([
                'newStatus' => 'required|boolean',
            ]);

            $blog = Blog::findOrFail($blogId);


            $like = BlogLikes::where('user_id', $user->id)
                ->where('blog_id', $blog->id)
                ->first();

            if ($request->newStatus) {
   
                if (!$like) {
                    BlogLikes::create([
                        'user_id' => $user->id,
                        'blog_id' => $blog->id,
                    ]);
                }
            } else {
                if ($like) {
                    $like->delete();
                }
            }

            return response()->json([
                'liked' => $request->newStatus,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => 'Something went wrong!',
                'message' => $th->getMessage(),
            ], 500);
        }
    }
}