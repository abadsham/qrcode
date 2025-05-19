<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\UserBook;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class CertificateController extends Controller
{
    public function publishAndDownloadCertificate($userBookId)
    {
        $userBook = UserBook::with('bookToken.book')->findOrFail($userBookId);

        $certificate = $userBook->certificate ?? new Certificate();
        $certificate->user_book_id = $userBook->id;
        $certificate->certificate_number = 'CERT-' . strtoupper(uniqid());
        // $certificate->secure_id = $userBook->secure_id;
        $certificate->save();

        // $pdf = Pdf::loadView('certificates.index', compact('userBook'));

        $html = "
            <h1 style='text-align: center;'>SERTIFIKAT KEPEMILIKAN</h1>
            <p>Nama: <strong>{$userBook->buyer_name}</strong></p>
            <p>Buku: <strong>{$userBook->bookToken->book->title}</strong></p>
            <p>ID Sertifikat: <strong>{$certificate->certificate_number}</strong></p>
            <p>Tanggal: <strong>" . now()->format('d-m-Y') . "</strong></p>
        ";

        $pdf = Pdf::loadHTML($html);
        $pdf->setPaper('A4', 'landscape');

        $fileName = $certificate->certificate_number . '.pdf';
        $filePath = 'certificates/' . $fileName;

        Storage::disk('public')->put($filePath, $pdf->output());

        $downloadUrl = asset('storage/' . $filePath);

        return response()->json([
            'message' => 'Certificate generated and Downloaded successfully',
            'download_url' => $downloadUrl,
            'certificate_number' => $certificate->certificate_number,
        ]);
        
        // return $pdf->download($certificate->certificate_number . '.pdf');
    }

    public function downloadCertificate($secure_id)
    {
        $certificate = Certificate::where('secure_id', $secure_id)->first();

        if (!$certificate) {
            return response()->json(['message' => 'Sertifikat belum tersedia'], 404);
        }

        $userBook = $certificate->userBook;

        $pdf = Pdf::loadView('certificates.index', compact('userBook'));

        $fileName = 'sertifikat-' . $userBook->secure_id . '.pdf';
        $filePath = 'certificates/' . $fileName;

        Storage::disk('public')->put($filePath, $pdf->output());

        $downloadUrl = asset('storage/' . $filePath);

        return response()->json([
            'message' => 'Sertifikat siap diunduh',
            'download_url' => $downloadUrl,
        ]);
    }
}
