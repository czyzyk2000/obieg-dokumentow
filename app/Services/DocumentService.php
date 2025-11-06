<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Document;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * DocumentService
 * 
 * Handles document CRUD operations and file management.
 * Separates business logic from controllers.
 */
class DocumentService
{
    /**
     * Create a new document
     */
    public function create(User $user, array $data): Document
    {
        $document = new Document($data);
        $document->user_id = $user->id;

        if (isset($data['file']) && $data['file'] instanceof UploadedFile) {
            $document->file_path = $this->storeFile($data['file']);
        }

        $document->save();

        return $document;
    }

    /**
     * Update existing document
     */
    public function update(Document $document, array $data): Document
    {
        if (isset($data['file']) && $data['file'] instanceof UploadedFile) {
            if ($document->hasFile()) {
                $this->deleteFile($document->file_path);
            }

            $document->file_path = $this->storeFile($data['file']);
        }

        if (isset($data['remove_file']) && $data['remove_file']) {
            if ($document->hasFile()) {
                $this->deleteFile($document->file_path);
                $document->file_path = null;
            }
        }

        $document->fill($data);
        $document->save();

        return $document;
    }

    /**
     * Delete document
     */
    public function delete(Document $document): bool
    {
        return $document->delete();
    }

    /**
     * Store uploaded file
     */
    protected function storeFile(UploadedFile $file): string
    {
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('', $filename, 'private_documents');

        return $path;
    }

    /**
     * Delete file from storage
     */
    protected function deleteFile(string $path): void
    {
        Storage::disk('private_documents')->delete($path);
    }

    /**
     * Get documents for current user based on their role
     */
    public function getDocumentsForUser(User $user)
    {
        return match ($user->role->value) {
            'user' => Document::where('user_id', $user->id)
                ->with('user')
                ->latest()
                ->paginate(15),

            'manager' => Document::where(function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->orWhereHas('user', fn($q) => $q->where('manager_id', $user->id));
            })
                ->with('user')
                ->latest()
                ->paginate(15),

            'finance' => Document::whereIn('status', [
                'pending_finance_approval',
                'approved',
                'rejected'
            ])
                ->with('user')
                ->latest()
                ->paginate(15),

            'admin' => Document::with('user')
                ->latest()
                ->paginate(15),

            default => Document::where('user_id', $user->id)
                ->with('user')
                ->latest()
                ->paginate(15),
        };
    }

    /**
     * Get documents pending approval for manager
     */
    public function getPendingForManager(User $manager)
    {
        return Document::pendingManagerApproval()
            ->forManager($manager)
            ->with('user')
            ->latest()
            ->get();
    }

    /**
     * Get documents pending approval for finance
     */
    public function getPendingForFinance()
    {
        return Document::pendingFinanceApproval()
            ->with('user')
            ->latest()
            ->get();
    }
}
