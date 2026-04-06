<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Document extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'document_number',
        'title',
        'category',
        'description',
        'file_path',
        'file_name',
        'file_type',
        'file_size',
        'documentable_type',
        'documentable_id',
        'uploaded_by',
        'version',
        'parent_document_id',
        'is_current_version',
        'status',
        'tags',
        'uploaded_at',
    ];

    protected $casts = [
        'tags' => 'array',
        'uploaded_at' => 'datetime',
        'is_current_version' => 'boolean',
    ];

    protected $appends = ['file_url', 'file_size_formatted', 'category_badge'];

    // Relationships
    public function documentable()
    {
        return $this->morphTo();
    }

    public function uploadedBy()
    {
        return $this->belongsTo(AdminUser::class, 'uploaded_by');
    }

    public function parentDocument()
    {
        return $this->belongsTo(Document::class, 'parent_document_id');
    }

    public function versions()
    {
        return $this->hasMany(Document::class, 'parent_document_id')->orderBy('version', 'desc');
    }

    // Accessors
    public function getFileUrlAttribute()
    {
        return Storage::url($this->file_path);
    }

    public function getFileSizeFormattedAttribute()
    {
        $bytes = $this->file_size;
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }

    public function getCategoryBadgeAttribute()
    {
        $badges = [
            'contract' => 'bg-blue-100 text-blue-800',
            'invoice' => 'bg-green-100 text-green-800',
            'certificate' => 'bg-purple-100 text-purple-800',
            'photo' => 'bg-pink-100 text-pink-800',
            'report' => 'bg-yellow-100 text-yellow-800',
            'other' => 'bg-gray-100 text-gray-800',
        ];

        return $badges[$this->category] ?? $badges['other'];
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeCurrentVersion($query)
    {
        return $query->where('is_current_version', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeUploadedBy($query, $userId)
    {
        return $query->where('uploaded_by', $userId);
    }

    // Methods
    public function canPreview()
    {
        $previewableTypes = ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'webp'];
        return in_array(strtolower($this->file_type), $previewableTypes);
    }

    public function getIconClass()
    {
        $icons = [
            'pdf' => 'fas fa-file-pdf text-red-500',
            'doc' => 'fas fa-file-word text-blue-500',
            'docx' => 'fas fa-file-word text-blue-500',
            'xls' => 'fas fa-file-excel text-green-500',
            'xlsx' => 'fas fa-file-excel text-green-500',
            'jpg' => 'fas fa-file-image text-purple-500',
            'jpeg' => 'fas fa-file-image text-purple-500',
            'png' => 'fas fa-file-image text-purple-500',
            'gif' => 'fas fa-file-image text-purple-500',
            'zip' => 'fas fa-file-archive text-yellow-500',
            'rar' => 'fas fa-file-archive text-yellow-500',
        ];

        return $icons[strtolower($this->file_type)] ?? 'fas fa-file text-gray-500';
    }

    public function createNewVersion($filePath, $fileName, $fileType, $fileSize, $uploadedBy)
    {
        // Mark current version as not current
        $this->update(['is_current_version' => false]);

        // Create new version
        return self::create([
            'document_number' => $this->document_number,
            'title' => $this->title,
            'category' => $this->category,
            'description' => $this->description,
            'file_path' => $filePath,
            'file_name' => $fileName,
            'file_type' => $fileType,
            'file_size' => $fileSize,
            'documentable_type' => $this->documentable_type,
            'documentable_id' => $this->documentable_id,
            'uploaded_by' => $uploadedBy,
            'version' => $this->version + 1,
            'parent_document_id' => $this->parent_document_id ?? $this->id,
            'is_current_version' => true,
            'status' => 'active',
            'tags' => $this->tags,
        ]);
    }

    public function archive()
    {
        return $this->update(['status' => 'archived']);
    }

    public function restore()
    {
        return $this->update(['status' => 'active']);
    }

    public function permanentlyDelete()
    {
        // Delete file from storage
        if (Storage::exists($this->file_path)) {
            Storage::delete($this->file_path);
        }

        // Delete all versions
        $this->versions()->each(function ($version) {
            if (Storage::exists($version->file_path)) {
                Storage::delete($version->file_path);
            }
            $version->forceDelete();
        });

        // Delete the document
        return $this->forceDelete();
    }

    // Static methods
    public static function generateDocumentNumber()
    {
        $prefix = 'DOC';
        $year = date('Y');
        $month = date('m');
        
        $lastDocument = self::where('document_number', 'like', "{$prefix}-{$year}{$month}-%")
            ->orderBy('document_number', 'desc')
            ->first();

        if ($lastDocument) {
            $lastNumber = intval(substr($lastDocument->document_number, -4));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return "{$prefix}-{$year}{$month}-{$newNumber}";
    }

    public static function getCategories()
    {
        return [
            'contract' => 'Contract',
            'invoice' => 'Invoice',
            'certificate' => 'Certificate',
            'photo' => 'Photo',
            'report' => 'Report',
            'other' => 'Other',
        ];
    }
}

// Made with Bob
