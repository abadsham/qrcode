<?php

use App\Http\Controllers\BookController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\UserScanController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('scan/{uuid}', [UserScanController::class, 'scanQr'])->name('user.scan-qr');
Route::post('scan/{uuid}/submit', [UserScanController::class, 'submitForm'])->name('user.submit-form');
Route::get('/certificates/download/{userBookId}', [CertificateController::class, 'publishAndDownloadCertificate'])->name('certificates.download');


Route::middleware('auth:sanctum')->group(function () {
    Route::resource('books', BookController::class);
    Route::get('books/{book}/qr', [BookController::class, 'generateQrCode'])->name('books.qr-code');
    Route::post('books/{book}/generate-token', [BookController::class, 'generateToken'])->name('books.generate-token');

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});

Route::post('/login', [LoginController::class, 'login'])->name('login');
