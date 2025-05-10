<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BlogLikes;
use App\Models\Blog;
use Illuminate\Support\Facades\Auth;

class BlogLikesController extends Controller
{
    public function fav_blog(Request $request, $blogId)
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



    public function getfav_blogs(Request $request)
    {
        $user = Auth::user();
    
        $fav = BlogLikes::where('user_id', $user->id)
            ->where('liked', true)
            ->with('blog')
            ->get()
            ->map(function ($fav) {
                if ($fav->blog) { 
                    return [
                        'id' => $fav->blog->id,
                        'name' => $fav->blog->name,
                        'img' => $fav->blog->img,
                        'description' => $fav->blog->description,
                    ];
                }
                return null; 
            })
            ->filter(); 
    
        return response()->json([
            'message' => 'Favorite blog retrieved',
            'favorite' => $fav->toArray(),
        ]);
    }
}