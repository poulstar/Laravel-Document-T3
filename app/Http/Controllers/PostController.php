<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Facades\DB;

class PostController extends Controller
{
    public function allPostsForDashboard()
    {
        $query = Post::query()
        ->select([
            'id',
            'user_id',
            'title',
            'description',
            'up_vote_count',
            DB::raw('ST_X(location::geometry) AS latitude'),
            DB::raw('ST_Y(location::geometry) AS longitude')
            ])
        ->with('media')
        ->with('user')
        ->orderBy('up_vote_count', 'desc');
        $posts = $query->paginate(4);
        $topPosts = $query->take(3)->get();
        return $this->successResponse([
            'posts' => $this->paginatedSuccessResponse($posts,'posts'),
            'topPosts' => $topPosts,
        ],200);
    }
}
