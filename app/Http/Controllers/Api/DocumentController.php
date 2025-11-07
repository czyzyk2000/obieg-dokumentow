<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreDocumentApiRequest;
use App\Http\Requests\Api\UpdateDocumentApiRequest;
use App\Http\Resources\DocumentCollection;
use App\Http\Resources\DocumentResource;
use App\Models\Document;
use App\Services\DocumentService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        protected DocumentService $documentService
    ) {}

    public function index(Request $request): DocumentCollection
    {
        $perPage = $request->input('per_page', 15);
        $perPage = min(max((int) $perPage, 1), 100);

        $user = $request->user();
        
        $documents = match ($user->role->value) {
            'user' => Document::where('user_id', $user->id)
                ->with('user')
                ->latest()
                ->paginate($perPage),

            'manager' => Document::where(function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->orWhereHas('user', fn($q) => $q->where('manager_id', $user->id));
            })
                ->with('user')
                ->latest()
                ->paginate($perPage),

            'finance' => Document::whereIn('status', [
                'pending_finance_approval',
                'approved',
                'rejected'
            ])
                ->with('user')
                ->latest()
                ->paginate($perPage),

            'admin' => Document::with('user')
                ->latest()
                ->paginate($perPage),

            default => Document::where('user_id', $user->id)
                ->with('user')
                ->latest()
                ->paginate($perPage),
        };

        return new DocumentCollection($documents);
    }

    public function store(StoreDocumentApiRequest $request): JsonResponse
    {
        $this->authorize('create', Document::class);

        $document = $this->documentService->create(
            $request->user(),
            $request->validated()
        );

        return response()->json([
            'message' => 'Document created successfully',
            'data' => new DocumentResource($document),
        ], 201);
    }

    public function show(Document $document): JsonResponse
    {
        $this->authorize('view', $document);

        $document->load(['user', 'history.user']);

        return response()->json([
            'data' => new DocumentResource($document),
        ], 200);
    }

    public function update(UpdateDocumentApiRequest $request, Document $document): JsonResponse
    {
        $this->authorize('update', $document);

        $this->documentService->update($document, $request->validated());

        return response()->json([
            'message' => 'Document updated successfully',
            'data' => new DocumentResource($document->fresh()),
        ], 200);
    }

    public function destroy(Document $document): JsonResponse
    {
        $this->authorize('delete', $document);

        $this->documentService->delete($document);

        return response()->json([
            'message' => 'Document deleted successfully',
        ], 200);
    }
}
