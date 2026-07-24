<?php

namespace App\Traits;

use App\Models\Document;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

trait HasDocuments
{
    /**
     * Get all documents for this model
     */
    public function documents()
    {
        return $this->morphMany(Document::class, 'documentable')
            ->currentVersion()
            ->active()
            ->orderBy('uploaded_at', 'desc');
    }

    /**
     * Get all documents including archived and old versions
     */
    public function allDocuments()
    {
        return $this->morphMany(Document::class, 'documentable')
            ->orderBy('uploaded_at', 'desc');
    }

    /**
     * Get documents by category
     */
    public function documentsByCategory($category)
    {
        return $this->documents()->where('category', $category)->get();
    }

    /**
     * Upload a new document
     */
    public function uploadDocument(
        UploadedFile $file,
        string $title,
        string $category,
        ?string $description = null,
        ?array $tags = null,
        ?int $uploadedBy = null
    ) {
        // Generate unique filename
        $fileName = $file->getClientOriginalName();
        $fileExtension = $file->getClientOriginalExtension();
        $fileType = strtolower($fileExtension);
        $fileSize = $file->getSize();
        
        // Store file
        $directory = 'documents/' . class_basename($this) . '/' . $this->id;
        $filePath = $file->store($directory, 'public');

        // Create document record
        return $this->documents()->create([
            'document_number' => Document::generateDocumentNumber(),
            'title' => $title,
            'category' => $category,
            'description' => $description,
            'file_path' => $filePath,
            'file_name' => $fileName,
            'file_type' => $fileType,
            'file_size' => $fileSize,
            'uploaded_by' => $uploadedBy ?? session('admin_user_id'),
            'version' => 1,
            'is_current_version' => true,
            'status' => 'active',
            'tags' => $tags,
            'uploaded_at' => now(),
        ]);
    }

    /**
     * Replace an existing document with a new version
     */
    public function replaceDocument(
        int $documentId,
        UploadedFile $file,
        ?int $uploadedBy = null
    ) {
        $document = $this->documents()->findOrFail($documentId);
        
        // Generate unique filename
        $fileName = $file->getClientOriginalName();
        $fileExtension = $file->getClientOriginalExtension();
        $fileType = strtolower($fileExtension);
        $fileSize = $file->getSize();
        
        // Store new file
        $directory = 'documents/' . class_basename($this) . '/' . $this->id;
        $filePath = $file->store($directory, 'public');

        // Create new version
        return $document->createNewVersion(
            $filePath,
            $fileName,
            $fileType,
            $fileSize,
            $uploadedBy ?? session('admin_user_id')
        );
    }

    /**
     * Delete a document
     */
    public function deleteDocument(int $documentId, bool $permanent = false)
    {
        $document = $this->documents()->findOrFail($documentId);
        
        if ($permanent) {
            return $document->permanentlyDelete();
        }
        
        return $document->delete();
    }

    /**
     * Archive a document
     */
    public function archiveDocument(int $documentId)
    {
        $document = $this->documents()->findOrFail($documentId);
        return $document->archive();
    }

    /**
     * Get document count by category
     */
    public function getDocumentCountByCategory()
    {
        return $this->documents()
            ->selectRaw('category, COUNT(*) as count')
            ->groupBy('category')
            ->pluck('count', 'category')
            ->toArray();
    }

    /**
     * Check if model has documents
     */
    public function hasDocuments()
    {
        return $this->documents()->exists();
    }

    /**
     * Check if model has documents in a specific category
     */
    public function hasDocumentsInCategory($category)
    {
        return $this->documents()->where('category', $category)->exists();
    }

    /**
     * Get the latest document in a category
     */
    public function getLatestDocumentInCategory($category)
    {
        return $this->documents()
            ->where('category', $category)
            ->orderBy('uploaded_at', 'desc')
            ->first();
    }

    /**
     * Get total storage used by documents (in bytes)
     */
    public function getTotalDocumentStorage()
    {
        return $this->documents()->sum('file_size');
    }

    /**
     * Get formatted total storage
     */
    public function getFormattedDocumentStorage()
    {
        $bytes = $this->getTotalDocumentStorage();
        
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }
        
        return $bytes . ' bytes';
    }
}

// Made with Bob
