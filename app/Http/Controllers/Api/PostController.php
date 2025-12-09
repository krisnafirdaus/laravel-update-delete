<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StorePostRequest;
use App\Models\Post;
use App\Http\Requests\UpdatePostRequest;
use App\Exceptions\BusinessException;
// use App\Rules\NoSpam;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Post::query('category');

        // searching by title
        if($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

         // searching by category
        if($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

         // searching by status
        if($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            $query->published();
        }

        // sorting
        $sortBy = $request->get('sort_by', 'published_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $posts = $query->paginate($request->get('per_page', 10));

        // transform data 
        $transformedPosts = collect($posts->items())->map(function ($post) {
            return [
                'id' => $post->id,
                'title' => $post->title,
                'slug' => $post->slug,
                'excerpt' => $post->excerpt,
                'status' => $post->status,
                'published_at' => $post->published_at,
                'category' => $post->category ? [
                    'id' => $post->category->id,
                    'name' => $post->category->name,
                    'slug' => $post->category->slug
                ] : null,
                'created_at' => $post->created_at
            ];
        });

        return response()->json([
            'success' => true,
            'message' => "Daftar posts ditampilan",
            'data' => $transformedPosts,
            'meta' => [
                'current_page' => $posts->currentPage(),
                'last_page' => $posts->lastPage(),
                'per_page' => $posts->perPage(),
                'total' => $posts->total(),
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePostRequest $request)
    {
        // $post = Post::create($request->validate([
        //     'title' => ['required', 'string', 'min:5', 'max:255', new NoSpam()]
        // ]));
        $post = Post::create($request->validate());
        $post->load('category');

        return response()->json([
            'success' => true,
            'message' => 'Post berhasil ditambahkan',
            'data' => $post
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        $post->load('category');
        
        return response()->json(
            [
                'success' => true,
                'message' => 'Detail Post',
                'data' => [
                    'id' => $post->id,
                    'title' => $post->title,
                    'slug' => $post->slug,
                    'excerpt' => $post->excerpt,
                    'status' => $post->status,
                    'published_at' => $post->published_at,
                    'category' => $post->category ? [
                        'id' => $post->category->id,
                        'name' => $post->category->name,
                        'slug' => $post->category->slug
                    ] : null,
                    'created_at' => $post->created_at,
                ]
            ]
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StorePostRequ $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        $postTitle = $post->title;
        $post->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data Dihapus'
        ]);
    }

    public function publish(Post $post){
        if($post->status === 'published') {
            throw new BussinessException('Post Sudah Ke Publish');
        }

        $post->publish();

         return response()->json([
            'success' => true,
            'message' => 'Post Berhasil di publish',
            'data' => $post->fresh()->load('category') 
        ]);
    }
}
