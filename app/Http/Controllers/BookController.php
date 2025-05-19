<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookToken;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Str;

class BookController extends Controller
{
    public function index()
    {
        $books = Book::withCount('book_token')->get();
        return response()->json($books);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $validatedData['uuid'] = Str::uuid();

        $book = Book::create($validatedData);

        $this->generateQrCode($book);

        return response()->json([
            'message' => 'Book created successfully',
            'book' => $book,
            'qr_url' => url('/api/books/' . $book->id . '/qr'),
        ]);
    }

    public function show(Book $book)
    {
        return response()->json($book);
    }

    public function update(Request $request, Book $book)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $book->update($validatedData);

        return response()->json([
            'message' => 'Book updated successfully',
            'book' => $book,
        ]);
    }

    public function destroy(Book $book)
    {
        $book->delete();

        return response()->json([
            'message' => 'Book deleted successfully',
        ]);
    }
    
    public function generateQrCode(Book $book)
    {
        $url = url('/api/scan/' . $book->uuid);
        $qr = QrCode::format('svg')->size(300)->generate(url($url));
        
        return response($qr)->header('Content-Type', 'image/png');
    }

    public function generateToken(Request $request, Book $book)
    {
        $request->validate([
            'count' => 'required|integer|min:1|max:1000'
        ]);

        $tokens = [];

        for ($i = 0; $i < $request->count; $i++) {
            $tokens[] = [
                'book_id' => $book->id,
                'token' => strtoupper(Str::random(6)),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        BookToken::insert($tokens);

        return response()->json([
            'message' => "{$request->count} Token generated successfully for book {$book->title}",
            'token' => $book->book_token,
        ]);
    }
}
