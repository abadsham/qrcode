<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookToken;
use App\Models\UserBook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserScanController extends Controller
{
    public function scanQr($uuid)
    {
        $book = Book::where('uuid', $uuid)->firstOrFail();

        return response()->json([
            'book_id' => $book->id,
            'title' => $book->title,
            'message' => 'Book Found, insert token to verify',
        ]);
    }

    public function submitForm(Request $request, $uuid)
    {
        $validatedData = $request->validate([
            'token' => 'required|string',
            'buyer_name' => 'required|string|max:255',
            'buyer_email' => 'required|email|max:255',
            'buyer_phone' => 'required|string|max:15',
        ],[
            'token.required' => 'Token tidak boleh kosong',
            'token.string' => 'Token harus berupa string',
            'buyer_name.required' => 'Nama pembeli tidak boleh kosong',
            'buyer_name.string' => 'Nama pembeli harus berupa string',
            'buyer_name.max' => 'Nama pembeli tidak boleh lebih dari 255 karakter',
            'buyer_email.required' => 'Email pembeli tidak boleh kosong',
            'buyer_email.email' => 'Format email pembeli tidak valid',
            'buyer_email.max' => 'Email pembeli tidak boleh lebih dari 255 karakter',
            'buyer_phone.required' => 'Nomor telepon pembeli tidak boleh kosong',
            'buyer_phone.string' => 'Nomor telepon pembeli harus berupa string',
            'buyer_phone.max' => 'Nomor telepon pembeli tidak boleh lebih dari 15 karakter',
        ]);

        $book = Book::where('uuid', $uuid)->firstOrFail();
        $token = BookToken::where('book_id', $book->id)->where('token', $validatedData['token'])->first();

        if(!$token) {
            return response()->json(['message' => 'Invalid token'], 400);
        }

        if($token->used) {
            return response()->json(['message' => 'Token already used'], 400);
        }

        DB::beginTransaction();

        try {
            $userBook = UserBook::create([
                'book_token_id' => $token->id,
                'buyer_name' => $validatedData['buyer_name'],
                'buyer_email' => $validatedData['buyer_email'],
                'buyer_phone' => $validatedData['buyer_phone'],
            ]);

            $token->update(['used' => true]);

            DB::commit();

            app(CertificateController::class)->publishAndDownloadCertificate($userBook->id);

            return response()->json([
                'message' => 'Ownership registered and certificate published successfully.',
                'certificate_download_url' => route('certificates.download', [
                    'userBookId' => $userBook->id
                ]),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'An error occurred while processing the request.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
