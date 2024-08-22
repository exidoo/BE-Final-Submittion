<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\BookRequest;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 12);
        $search = $request->get('search', '');
        $paginate = $request->get('paginate', true);

        $query = Book::with('category')
            ->when($search, function ($query, $search) {
                return $query->where('title', 'like', "%{$search}%");
            })
            ->orderBy('created_at', 'desc');

        if ($paginate) {
            $books = $query->paginate($perPage);
        } else {
            $books = $query->get();
        }

        return response()->json($books);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $book = Book::with('category')->find($id);

        if (!$book) {
            return response()->json(['message' => 'Book tidak ditemukan'], 404);
        }

        return response()->json($book);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BookRequest $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $uploadedFileUrl = Cloudinary::upload($request->file('image')->getRealPath())->getSecurePath();
            Log::info('Uploaded URL: ' . $uploadedFileUrl);
            $data['image'] = $uploadedFileUrl;
        }

        $book = Book::create($data);

        $book->load('category');
        return response()->json([
            'message' => 'Book baru berhasil dibuat',
            'data' => $book
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BookRequest $request, string $id)
    {
        Log::info('Received Update Book Request:', $request->all());

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $book = Book::findOrFail($id);

        if ($request->hasFile('image')) {
            if ($book->image) {
                Cloudinary::destroy(basename($book->image));
            }

            $uploadedFileUrl = Cloudinary::upload($request->file('image')->getRealPath())->getSecurePath();
            $data['image'] = $uploadedFileUrl;
        }

        $book->update($data);
        $book->load('category');

        return response()->json([
            'message' => 'Book berhasil diperbarui',
            'data' => $book
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $book = Book::findOrFail($id);

        if ($book->image) {
            Cloudinary::destroy(basename($book->image));
        }

        $book->delete();

        return response()->json([
            'message' => 'Book berhasil dihapus',
        ]);
    }
}
