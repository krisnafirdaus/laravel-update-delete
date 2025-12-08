<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Category;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Category::query();

        // searching by name
        if($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // include posts count
        $query->withCount('posts');

        // sorting
        $sortBy = $request->get('sort_by', 'name');
        $sortOrder = $request->get('sort_order', 'asc');
        $query->orderBy($sortBy, $sortOrder);

        // pagination 
        if($request->boolean('all'))
        {
            $categories = $query->get();
            return response()->json([
                'success' => true,
                'message' => "Daftar All Categories ditampilan",
                'data' => $categories
            ]);
        }

        $categories = $query->paginate($request->get('per_page', 10));

        return response()->json([
            'success' => true,
            'message' => "Daftar Categories ditampilan",
            'data' => $categories->items(),
            'meta' => [
                'current_page' => $categories->currentPage(),
                'last_page' => $categories->lastPage(),
                'per_page' => $categories->perPage(),
                'total' => $categories->total(),
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request,  Category $category): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|min:2|max:100|unique:categories,name',
            'slug' => 'nullable|string|unique:categories,slug',
            'description' => 'nullable|string|max:500',
        ], [
            'name.required' => 'Nama Kategori harus diisi',
            'name.min' => 'Nama Kategori minimal 2 karakter',
            'name.max' => 'Nama Kategori maksimal 100 karakter',
            'name.unique' => 'Nama Kategori sudah ada',
            'slug.unique' => 'Slug Kategori sudah ada',
            'description.max' => 'Deskripsi Kategori maksimal 500 karakter',
        ]);

        if(empty($validated['slug'])){
            $validated['slug'] = Str::slug($validated['name']);
        }

        $category = Category::create($validated);

        return response()->json([
            'success' => true,
            'message' => "Category berhasil ditambahkan",
            'data' => $category
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category): JsonResponse
    {
        $category->loadCount('posts');

        return response()->json([
            'success' => true,
            'message' => "Detail Category",
            'data' => $category
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category): JsonResponse
    {
          $validated = $request->validate([
            'name' => 'required|string|min:2|max:100|unique:categories,name',
            'slug' => 'nullable|string|unique:categories,slug',
            'description' => 'nullable|string|max:500',
        ], [
            'name.required' => 'Nama Kategori harus diisi',
            'name.min' => 'Nama Kategori minimal 2 karakter',
            'name.max' => 'Nama Kategori maksimal 100 karakter',
            'name.unique' => 'Nama Kategori sudah ada',
            'slug.unique' => 'Slug Kategori sudah ada',
            'description.max' => 'Deskripsi Kategori maksimal 500 karakter',
        ]);

        if(isset($validated['name']) && empty($validated['slug'])){
            $validated['slug'] = Str::slug($validated['name']);
        }

        $category->update($validated);

        return response()->json([
            'success' => true,
            'message' => "Category berhasil diupdated",
            'data' => $category->fresh()
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category): JsonResponse
    {
        if($category->posts()->count() > 0)
        {
            return response()->json([
                'success' => true,
                'message' => 'Kategori tidak dapat dihapus karena masih memiliki data posts'
            ], 422);
        }

        $categoryName = $category->name;
        $category->delete();

        return response()->json([
            'success' => true,
            'message' => "Category $categoryName berhasil dihapus",
        ]);
    }
}
