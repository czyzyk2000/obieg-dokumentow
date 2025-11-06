<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ApprovalRequest;
use App\Models\Document;
use App\Services\DocumentWorkflowService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;

/**
 * DocumentApprovalController
 * 
 * Handles document approval workflow actions.
 * Implements state machine transitions.
 */
class DocumentApprovalController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        protected DocumentWorkflowService $workflowService
    ) {}

    /**
     * Submit document for approval
     */
    public function submit(Document $document): RedirectResponse
    {
        $this->authorize('submit', $document);

        try {
            $this->workflowService->submit($document, auth()->user());

            return redirect()
                ->route('documents.show', $document)
                ->with('success', 'Dokument został wysłany do akceptacji.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Wystąpił błąd podczas wysyłania dokumentu: ' . $e->getMessage());
        }
    }

    /**
     * Approve document as manager
     */
    public function approveByManager(ApprovalRequest $request, Document $document): RedirectResponse
    {
        $this->authorize('approveAsManager', $document);

        try {
            $this->workflowService->approveByManager(
                $document,
                auth()->user(),
                $request->input('comment')
            );

            return redirect()
                ->route('documents.show', $document)
                ->with('success', 'Dokument został zaakceptowany.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Wystąpił błąd podczas akceptacji: ' . $e->getMessage());
        }
    }

    /**
     * Reject document as manager
     */
    public function rejectByManager(ApprovalRequest $request, Document $document): RedirectResponse
    {
        $this->authorize('rejectAsManager', $document);

        $comment = $request->input('comment');

        if (empty($comment)) {
            return redirect()
                ->back()
                ->with('error', 'Komentarz jest wymagany przy odrzuceniu dokumentu.');
        }

        try {
            $this->workflowService->rejectByManager(
                $document,
                auth()->user(),
                $comment
            );

            return redirect()
                ->route('documents.show', $document)
                ->with('success', 'Dokument został odrzucony.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Wystąpił błąd podczas odrzucania: ' . $e->getMessage());
        }
    }

    /**
     * Approve document as finance
     */
    public function approveByFinance(ApprovalRequest $request, Document $document): RedirectResponse
    {
        $this->authorize('approveAsFinance', $document);

        try {
            $this->workflowService->approveByFinance(
                $document,
                auth()->user(),
                $request->input('comment')
            );

            return redirect()
                ->route('documents.show', $document)
                ->with('success', 'Dokument został zaakceptowany przez dział finansowy.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Wystąpił błąd podczas akceptacji: ' . $e->getMessage());
        }
    }

    /**
     * Reject document as finance
     */
    public function rejectByFinance(ApprovalRequest $request, Document $document): RedirectResponse
    {
        $this->authorize('rejectAsFinance', $document);

        $comment = $request->input('comment');

        if (empty($comment)) {
            return redirect()
                ->back()
                ->with('error', 'Komentarz jest wymagany przy odrzuceniu dokumentu.');
        }

        try {
            $this->workflowService->rejectByFinance(
                $document,
                auth()->user(),
                $comment
            );

            return redirect()
                ->route('documents.show', $document)
                ->with('success', 'Dokument został odrzucony przez dział finansowy.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Wystąpił błąd podczas odrzucania: ' . $e->getMessage());
        }
    }
}
