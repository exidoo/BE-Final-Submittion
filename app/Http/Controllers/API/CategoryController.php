<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $category = Category::with('books')->get();
        return response()->json($category);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CategoryRequest $request)
    {
        $category = Category::create($request->all());
        return response()->json([
            'message' => 'Category baru berhasil dibuat',
            'data' => $category
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        // Cari category berdasarkan ID
        $category = Category::find($id);

        // Jika category tidak ditemukan, kembalikan response 404
        if (!$category) {
            return response()->json([
                'message' => 'Category tidak ditemukan'
            ], 404);
        }

        // Tentukan jumlah item per halaman, defaultnya adalah 12
        $perPage = $request->get('per_page', 12);

        // Ambil parameter pencarian dari query string
        $search = $request->get('search', '');

        // Lakukan query pada relasi books
        $booksQuery = $category->books()->when($search, function ($query, $search) {
            return $query->where('title', 'like', "%{$search}%");
        });

        // Paginate hasil query books
        $books = $booksQuery->paginate($perPage);

        return response()->json([
            "message" => "Detail category id ditemukan",
            "data" => [
                'category' => $category,
                'books' => $books
            ]
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CategoryRequest $request, string $id)
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json([
                'message' => 'Category tidak ditemukan'
            ], 404);
        }
        $category->update($request->all());
        return response()->json([
            'message' => 'Category berhasil diupdate',
            'data' => $category
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json([
                'message' => 'Category tidak ditemukan'
            ], 404);
        }
        $category->delete();
        return response()->json([
            'message' => 'Category berhasil dihapus'
        ], 200);
    }
}
