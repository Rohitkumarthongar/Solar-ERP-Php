<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function upload(Request $request)
    {
        if (!session('admin_logged_in')) {
            return redirect()->route('admin.login');
        }

        $validated = $request->validate([
            'file' => 'required|file|max:10240', // 10MB max
            'title' => 'required|string|max:255',
            'category' => 'required|in:contract,invoice,certificate,photo,report,other',
            'description' => 'nullable|string',
            'tags' => 'nullable|string',
            'model_type' => 'required|string',
            'model_id' => 'required|integer',
        ]);

        try {
            // Get the model
            $modelClass = 'App\\Models\\' . $validated['model_type'];
            if (!class_exists($modelClass)) {
                return back()->withErrors(['error' => 'Invalid model type']);
            }

            $model = $modelClass::findOrFail($validated['model_id']);

            // Process tags
            $tags = null;
            if ($request->filled('tags')) {
                $tags = array_map('trim', explode(',', $validated['tags']));
            }

            // Upload document
            $document = $model->uploadDocument(
                $request->file('file'),
                $validated['title'],
                $validated['category'],
                $validated['description'] ?? null,
                $tags,
                session('admin_user_id')
            );

            return back()->with('success', 'Document uploaded successfully!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to upload document: ' . $e->getMessage()]);
        }
    }

    public function download($id)
    {
        if (!session('admin_logged_in')) {
            return redirect()->route('admin.login');
        }

        $document = Document::findOrFail($id);

        if (!Storage::exists($document->file_path)) {
            abort(404, 'File not found');
        }

        return Storage::download($document->file_path, $document->file_name);
    }

    public function preview($id)
    {
        if (!session('admin_logged_in')) {
            return redirect()->route('admin.login');
        }

        $document = Document::findOrFail($id);

        if (!$document->canPreview()) {
            return back()->withErrors(['error' => 'This file type cannot be previewed']);
        }

        if (!Storage::exists($document->file_path)) {
            abort(404, 'File not found');
        }

        $fileContent = Storage::get($document->file_path);
        $mimeType = Storage::mimeType($document->file_path);

        return response($fileContent)
            ->header('Content-Type', $mimeType)
            ->header('Content-Disposition', 'inline; filename="' . $document->file_name . '"');
    }

    public function versions($id)
    {
        if (!session('admin_logged_in')) {
            return redirect()->route('admin.login');
        }

        $document = Document::with(['versions', 'parentDocument'])->findOrFail($id);
        
        // Get all versions including the parent
        $allVersions = collect();
        
        if ($document->parent_document_id) {
            // This is a version, get the parent and all its versions
            $parent = $document->parentDocument;
            $allVersions->push($parent);
            $allVersions = $allVersions->merge($parent->versions);
        } else {
            // This is the parent, get it and all its versions
            $allVersions->push($document);
            $allVersions = $allVersions->merge($document->versions);
        }
        
        $allVersions = $allVersions->sortByDesc('version');

        return view('admin.documents.versions', compact('document', 'allVersions'));
    }

    public function replace(Request $request, $id)
    {
        if (!session('admin_logged_in')) {
            return redirect()->route('admin.login');
        }

        $validated = $request->validate([
            'file' => 'required|file|max:10240', // 10MB max
        ]);

        try {
            $document = Document::findOrFail($id);
            $model = $document->documentable;

            if (!$model) {
                return back()->withErrors(['error' => 'Associated model not found']);
            }

            // Replace document with new version
            $newVersion = $model->replaceDocument(
                $id,
                $request->file('file'),
                session('admin_user_id')
            );

            return back()->with('success', 'Document replaced successfully! New version: v' . $newVersion->version);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to replace document: ' . $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        if (!session('admin_logged_in')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        try {
            $document = Document::findOrFail($id);
            $model = $document->documentable;

            if (!$model) {
                return response()->json(['success' => false, 'message' => 'Associated model not found'], 404);
            }

            // Soft delete the document
            $model->deleteDocument($id, false);

            return response()->json(['success' => true, 'message' => 'Document deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to delete document: ' . $e->getMessage()], 500);
        }
    }

    public function archive($id)
    {
        if (!session('admin_logged_in')) {
            return redirect()->route('admin.login');
        }

        try {
            $document = Document::findOrFail($id);
            $model = $document->documentable;

            if (!$model) {
                return back()->withErrors(['error' => 'Associated model not found']);
            }

            $model->archiveDocument($id);

            return back()->with('success', 'Document archived successfully');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to archive document: ' . $e->getMessage()]);
        }
    }

    public function restore($id)
    {
        if (!session('admin_logged_in')) {
            return redirect()->route('admin.login');
        }

        try {
            $document = Document::withTrashed()->findOrFail($id);
            $document->restore();

            return back()->with('success', 'Document restored successfully');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to restore document: ' . $e->getMessage()]);
        }
    }

    public function permanentDelete($id)
    {
        if (!session('admin_logged_in')) {
            return redirect()->route('admin.login');
        }

        try {
            $document = Document::withTrashed()->findOrFail($id);
            $model = $document->documentable;

            if (!$model) {
                return back()->withErrors(['error' => 'Associated model not found']);
            }

            $model->deleteDocument($id, true);

            return back()->with('success', 'Document permanently deleted');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to delete document: ' . $e->getMessage()]);
        }
    }
}

// Made with Bob
