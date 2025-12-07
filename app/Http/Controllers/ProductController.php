<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    public function index(Request $request): JsonResponse {
        //  $query = Product::query();
        //  $product = $query->paginate(10);
        $products = Product::all();

         return response()->json(
            [
                'success' => true,
                'message' => 'Daftar Produk berhasil ditambahkan',
                'data' => $products,
            ]
        );
    }

    public function show($id): JsonResponse {
        $product = Product::find($id);
        
        return response()->json(
            [
                'success' => true,
                'message' => 'Produk berhasil ditemukan',
                'data' => $product
            ]
        ); 
    }

    // store punya data json -> tipe data 
    public function store(Request $request): JsonResponse {
        // validasi input
        $validated = $request->validate([
            'name' => 'required|string|min:3|max:255',
            'description' => 'nullable|string|max:1000',
            'price' => 'required|integer|min:0',
            'stock' => 'required|integer|min:0',
        ], [
            'name.required' => 'Nama Produk harus diisi',
            'name.min' => 'Nama Produk minimal 3 karakter',
            'price.required' => 'Harga produk harus diisi',
            'stock.required' => 'Stock produk harus diisi',
        ]);
        // simpan ke database
        $product = Product::create($validated);
        // return response

        return response()->json(
            [
                'success' => true,
                'message' => 'Produk berhasil ditambahkan',
                'data' => $product
            ]
        );
    }

    public function update(Request $request, Product $product): JsonResponse {
        // validasi input
         $validated = $request->validate([
            'name' => 'sometimes|required|string|min:3|max:255', // put dan patch
            'description' => 'nullable|string|max:1000',
            'price' => 'sometimes|required|integer|min:0',
            'stock' => 'sometimes|required|integer|min:0',
        ], [
            'name.required' => 'Nama Produk harus diisi',
            'name.min' => 'Nama Produk minimal 3 karakter',
            'price.required' => 'Harga produk harus diisi',
            'stock.required' => 'Stock produk harus diisi',
        ]);

        // update harga
        $product->update($validated);

        // return response 
        return response()->json(
            [
                'success' => true,
                'message' => 'Produk berhasil diperbarui',
                'data' => $product->fresh() // refresh data
            ]
        );
    }

    public function destroy(Product $product): JsonResponse {
        $productName = $product->name;
        $product->delete(); // soft delete

        return response()->json(
            [
                'success' => true,
                'message' => "Produk '{$productName}' berhasil dihapus",
            ]
        );
    }

    public function trash(): JsonResponse {
        $deletedProducts = Product::onlyTrashed()->get();

        return response()->json(
            [
                'success' => true,
                'message' => "Daftar produk yang dihapus",
                'data' => $deletedProducts,
            ]
        );
    }

    public function restore(string $id): JsonResponse {
        $product = Product::onlyTrashed()->findOrFail($id);
        $product->restore();

        return response()->json(
            [
                'success' => true,
                'message' => "Product '{$product->name}' berhasil direstore",
                'data' => $product,
            ]
        );
    }

    public function forceDelete(string $id): JsonResponse {
        $product = Product::onlyTrashed()->findOrFail($id);
        $productName = $product->name;
        $product->forceDelete();

        return response()->json(
            [
                'success' => true,
                'message' => "Produk '{$productName}' berhasil dihapus",
            ]
        );
    }
}
