<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * DocumentFileController
 * 
 * Handles secure file downloads for documents.
 * Implements authorization checks before serving files.
 */
class DocumentFileController extends Controller
{
    use AuthorizesRequests;

    /**
     * Download document file
     */
    public function download(Document $document): BinaryFileResponse|StreamedResponse|Response
    {
        $this->authorize('downloadFile', $document);

        if (!$document->hasFile()) {
            abort(404, 'Plik nie został znaleziony.');
        }

        $filePath = Storage::disk('private_documents')->path($document->file_path);

        if (!file_exists($filePath)) {
            abort(404, 'Plik nie istnieje w systemie plików.');
        }

        return response()->download(
            $filePath,
            $document->file_name,
            [
                'Content-Type' => Storage::disk('private_documents')->mimeType($document->file_path),
            ]
        );
    }
}
