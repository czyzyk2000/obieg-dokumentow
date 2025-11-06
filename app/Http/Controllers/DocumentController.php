<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreDocumentRequest;
use App\Http\Requests\UpdateDocumentRequest;
use App\Models\Document;
use App\Services\DocumentService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * DocumentController
 * 
 * Handles CRUD operations for documents.
 * Uses service layer for business logic.
 */
class DocumentController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        protected DocumentService $documentService
    ) {}

    /**
     * Display a listing of documents
     */
    public function index(): View
    {
        $documents = $this->documentService->getDocumentsForUser(auth()->user());

        $pendingCount = 0;
        if (auth()->user()->isManager()) {
            $pendingCount = $this->documentService->getPendingForManager(auth()->user())->count();
        } elseif (auth()->user()->isFinance()) {
            $pendingCount = $this->documentService->getPendingForFinance()->count();
        }

        return view('documents.index', compact('documents', 'pendingCount'));
    }

    /**
     * Show the form for creating a new document
     */
    public function create(): View
    {
        $this->authorize('create', Document::class);

        return view('documents.create');
    }

    /**
     * Store a newly created document
     */
    public function store(StoreDocumentRequest $request): RedirectResponse
    {
        $this->authorize('create', Document::class);

        $document = $this->documentService->create(
            auth()->user(),
            $request->validated()
        );

        return redirect()
            ->route('documents.show', $document)
            ->with('success', 'Dokument został utworzony pomyślnie.');
    }

    /**
     * Display the specified document
     */
    public function show(Document $document): View
    {
        $this->authorize('view', $document);

        $document->load(['user', 'history.user']);

        return view('documents.show', compact('document'));
    }

    /**
     * Show the form for editing the specified document
     */
    public function edit(Document $document): View
    {
        $this->authorize('update', $document);

        return view('documents.edit', compact('document'));
    }

    /**
     * Update the specified document
     */
    public function update(UpdateDocumentRequest $request, Document $document): RedirectResponse
    {
        $this->authorize('update', $document);

        $this->documentService->update($document, $request->validated());

        return redirect()
            ->route('documents.show', $document)
            ->with('success', 'Dokument został zaktualizowany pomyślnie.');
    }

    /**
     * Remove the specified document
     */
    public function destroy(Document $document): RedirectResponse
    {
        $this->authorize('delete', $document);

        $this->documentService->delete($document);

        return redirect()
            ->route('documents.index')
            ->with('success', 'Dokument został usunięty pomyślnie.');
    }
}
