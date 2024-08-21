<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\BorrowRequest;
use App\Models\Borrow;
use App\Models\Book;

class BorrowController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Ambil jumlah item per halaman dari query parameter, default ke 10 jika tidak ada
        $perPage = $request->get('per_page', 10);

        // Ambil data dengan pagination
        $borrow = Borrow::with(['book', 'user'])->paginate($perPage);

        return response()->json($borrow);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BorrowRequest $request)
    {
        $data = $request->validated();
        $borrow = Borrow::create($data);

        // Kurangi stok buku
        $book = Book::find($data['book_id']);
        if ($book->stok > 0) {
            $book->decrement('stok');
        } else {
            return response()->json([
                'message' => 'Stok buku habis',
            ], 400);
        }

        // Load the related book and user
        $borrow->load(['book', 'user']);

        return response()->json([
            'message' => 'Peminjaman baru berhasil dibuat',
            'data' => $borrow
        ], 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BorrowRequest $request, string $id)
    {
        $data = $request->validated();
        $borrow = Borrow::find($id);
        if (!$borrow) {
            return response()->json([
                'message' => 'Peminjaman tidak ditemukan'
            ], 404);
        }

        // Tambahkan kembali stok buku lama jika buku diganti
        if ($borrow->book_id !== $data['book_id']) {
            $oldBook = Book::find($borrow->book_id);
            $oldBook->increment('stok');
        }

        // Kurangi stok buku yang baru
        $newBook = Book::find($data['book_id']);
        if ($newBook->stok > 0) {
            $newBook->decrement('stok');
        } else {
            return response()->json([
                'message' => 'Stok buku habis',
            ], 400);
        }

        $borrow->update($data);

        // Load the related book and user
        $borrow->load(['book', 'user']);

        return response()->json([
            'message' => 'Peminjaman berhasil diupdate',
            'data' => $borrow
        ], 201);
    }
}
