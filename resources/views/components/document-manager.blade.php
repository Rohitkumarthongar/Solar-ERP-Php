@props(['model', 'modelType', 'modelId'])

<div class="document-manager bg-white rounded-lg shadow-sm border border-gray-200 p-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-lg font-semibold text-gray-900">
            <i class="fas fa-folder-open mr-2 text-blue-500"></i>
            Documents
        </h3>
        <button type="button" 
                onclick="openUploadModal()"
                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
            <i class="fas fa-upload mr-2"></i>Upload Document
        </button>
    </div>

    <!-- Category Filter -->
    <div class="flex flex-wrap gap-2 mb-6">
        <button onclick="filterByCategory('all')" 
                class="category-filter px-4 py-2 rounded-lg text-sm font-medium transition active"
                data-category="all">
            All Documents
        </button>
        @foreach(\App\Models\Document::getCategories() as $key => $label)
        <button onclick="filterByCategory('{{ $key }}')" 
                class="category-filter px-4 py-2 rounded-lg text-sm font-medium transition"
                data-category="{{ $key }}">
            {{ $label }}
        </button>
        @endforeach
    </div>

    <!-- Documents List -->
    <div id="documents-list" class="space-y-3">
        @forelse($model->documents as $document)
        <div class="document-item border border-gray-200 rounded-lg p-4 hover:shadow-md transition"
             data-category="{{ $document->category }}">
            <div class="flex items-start justify-between">
                <!-- Document Info -->
                <div class="flex items-start space-x-4 flex-1">
                    <!-- Icon -->
                    <div class="flex-shrink-0">
                        <i class="{{ $document->getIconClass() }} text-3xl"></i>
                    </div>
                    
                    <!-- Details -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center space-x-2 mb-1">
                            <h4 class="text-sm font-semibold text-gray-900 truncate">
                                {{ $document->title }}
                            </h4>
                            <span class="px-2 py-1 text-xs font-medium rounded {{ $document->category_badge }}">
                                {{ ucfirst($document->category) }}
                            </span>
                            @if($document->version > 1)
                            <span class="px-2 py-1 text-xs font-medium rounded bg-purple-100 text-purple-800">
                                v{{ $document->version }}
                            </span>
                            @endif
                        </div>
                        
                        <p class="text-xs text-gray-500 mb-2">
                            {{ $document->file_name }} • {{ $document->file_size_formatted }}
                        </p>
                        
                        @if($document->description)
                        <p class="text-sm text-gray-600 mb-2">{{ $document->description }}</p>
                        @endif
                        
                        <div class="flex items-center space-x-4 text-xs text-gray-500">
                            <span>
                                <i class="fas fa-user mr-1"></i>
                                {{ $document->uploadedBy->name ?? 'Unknown' }}
                            </span>
                            <span>
                                <i class="fas fa-clock mr-1"></i>
                                {{ $document->uploaded_at->format('d M Y, H:i') }}
                            </span>
                            @if($document->tags)
                            <div class="flex flex-wrap gap-1">
                                @foreach($document->tags as $tag)
                                <span class="px-2 py-0.5 bg-gray-100 text-gray-600 rounded text-xs">
                                    #{{ $tag }}
                                </span>
                                @endforeach
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center space-x-2 ml-4">
                    @if($document->canPreview())
                    <button onclick="previewDocument({{ $document->id }})"
                            class="p-2 text-blue-600 hover:bg-blue-50 rounded transition"
                            title="Preview">
                        <i class="fas fa-eye"></i>
                    </button>
                    @endif
                    
                    <a href="{{ route('admin.documents.download', $document->id) }}"
                       class="p-2 text-green-600 hover:bg-green-50 rounded transition"
                       title="Download">
                        <i class="fas fa-download"></i>
                    </a>
                    
                    <button onclick="showVersionHistory({{ $document->id }})"
                            class="p-2 text-purple-600 hover:bg-purple-50 rounded transition"
                            title="Version History">
                        <i class="fas fa-history"></i>
                    </button>
                    
                    <button onclick="replaceDocument({{ $document->id }})"
                            class="p-2 text-yellow-600 hover:bg-yellow-50 rounded transition"
                            title="Replace">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                    
                    <button onclick="deleteDocument({{ $document->id }})"
                            class="p-2 text-red-600 hover:bg-red-50 rounded transition"
                            title="Delete">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
        @empty
        <div class="text-center py-12 text-gray-500">
            <i class="fas fa-folder-open text-5xl mb-4 text-gray-300"></i>
            <p class="text-lg font-medium">No documents uploaded yet</p>
            <p class="text-sm">Click "Upload Document" to add files</p>
        </div>
        @endforelse
    </div>
</div>

<!-- Upload Modal -->
<div id="uploadModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-semibold text-gray-900">Upload Document</h3>
                <button onclick="closeUploadModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <form action="{{ route('admin.documents.upload') }}" method="POST" enctype="multipart/form-data" id="uploadForm">
                @csrf
                <input type="hidden" name="model_type" value="{{ $modelType }}">
                <input type="hidden" name="model_id" value="{{ $modelId }}">
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">File *</label>
                        <input type="file" name="file" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <p class="text-xs text-gray-500 mt-1">Max file size: 10MB</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Title *</label>
                        <input type="text" name="title" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Category *</label>
                        <select name="category" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="">Select Category</option>
                            @foreach(\App\Models\Document::getCategories() as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <textarea name="description" rows="3"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"></textarea>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tags (comma-separated)</label>
                        <input type="text" name="tags" placeholder="e.g., important, contract, 2024"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="closeUploadModal()"
                            class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        <i class="fas fa-upload mr-2"></i>Upload
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openUploadModal() {
    document.getElementById('uploadModal').classList.remove('hidden');
}

function closeUploadModal() {
    document.getElementById('uploadModal').classList.add('hidden');
    document.getElementById('uploadForm').reset();
}

function filterByCategory(category) {
    const items = document.querySelectorAll('.document-item');
    const filters = document.querySelectorAll('.category-filter');
    
    // Update active filter
    filters.forEach(f => f.classList.remove('active', 'bg-blue-600', 'text-white'));
    const activeFilter = document.querySelector(`[data-category="${category}"]`);
    activeFilter.classList.add('active', 'bg-blue-600', 'text-white');
    
    // Filter items
    items.forEach(item => {
        if (category === 'all' || item.dataset.category === category) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
}

function previewDocument(id) {
    window.open(`/admin/documents/${id}/preview`, '_blank');
}

function showVersionHistory(id) {
    window.location.href = `/admin/documents/${id}/versions`;
}

function replaceDocument(id) {
    if (confirm('Are you sure you want to replace this document? The current version will be archived.')) {
        // Implement replace logic
        alert('Replace functionality - to be implemented');
    }
}

function deleteDocument(id) {
    if (confirm('Are you sure you want to delete this document? This action cannot be undone.')) {
        fetch(`/admin/documents/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error deleting document');
            }
        });
    }
}

// Initialize with all documents visible
document.addEventListener('DOMContentLoaded', function() {
    const firstFilter = document.querySelector('.category-filter[data-category="all"]');
    if (firstFilter) {
        firstFilter.classList.add('bg-blue-600', 'text-white');
    }
});
</script>

<style>
.category-filter {
    background-color: #f3f4f6;
    color: #4b5563;
}

.category-filter:hover {
    background-color: #e5e7eb;
}

.category-filter.active {
    background-color: #2563eb;
    color: white;
}
</style>

// Made with Bob
