<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\BookRequest;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Tentukan jumlah item per halaman, defaultnya adalah 12
        $perPage = $request->get('per_page', 12);

        // Ambil parameter pencarian dari query string
        $search = $request->get('search', '');

        // Cek apakah pagination perlu diaktifkan atau tidak
        $paginate = $request->get('paginate', true);

        // Query dasar untuk mengambil buku beserta kategori
        $query = Book::with('category')
            ->when($search, function ($query, $search) {
                return $query->where('title', 'like', "%{$search}%");
            })
            ->orderBy('created_at', 'desc'); // Mengurutkan berdasarkan created_at dari yang terbaru

        // Jika pagination diaktifkan, kembalikan hasil paginated
        if ($paginate) {
            $books = $query->paginate($perPage);
        } else {
            // Jika pagination tidak diaktifkan, ambil semua data buku
            $books = $query->get();
        }

        // Kembalikan response dalam format JSON
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
        $data =  $request->validate([
            'file' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Jika file gambar diinput
        if ($request->hasFile('image')) {
            // Unggah gambar ke Cloudinary
            $uploadedFileUrl = Cloudinary::upload($request->file('image')->getRealPath())->getSecurePath();

            // Simpan URL gambar dari Cloudinary ke dalam database
            $data['image'] = $uploadedFileUrl;
        }

        $book = Book::create($data);

        // Muat relasi category dan kembalikan respon
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

        $data = $request->validated();

        $book = Book::findOrFail($id);

        if ($request->hasFile('image')) {
            // Hapus gambar lama dari Cloudinary jika ada
            if ($book->image) {
                Cloudinary::destroy(basename($book->image));
            }

            // Unggah gambar baru ke Cloudinary
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

        // Hapus gambar dari Cloudinary jika ada
        if ($book->image) {
            Cloudinary::destroy(basename($book->image));
        }

        $book->delete();

        return response()->json([
            'message' => 'Book berhasil dihapus',
        ]);
    }
}
