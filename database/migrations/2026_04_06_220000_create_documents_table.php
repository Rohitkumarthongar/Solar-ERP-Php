<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('document_number')->unique();
            $table->string('title');
            $table->string('category'); // contract, invoice, certificate, photo, report, other
            $table->text('description')->nullable();
            $table->string('file_path');
            $table->string('file_name');
            $table->string('file_type'); // pdf, jpg, png, docx, etc.
            $table->integer('file_size'); // in bytes
            $table->morphs('documentable'); // polymorphic relation
            $table->foreignId('uploaded_by')->nullable()->constrained('admin_users')->nullOnDelete();
            $table->integer('version')->default(1);
            $table->foreignId('parent_document_id')->nullable()->constrained('documents')->nullOnDelete(); // for version history
            $table->boolean('is_current_version')->default(true);
            $table->string('status')->default('active'); // active, archived, deleted
            $table->text('tags')->nullable(); // JSON array of tags
            $table->timestamp('uploaded_at')->useCurrent();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('category');
            $table->index('uploaded_by');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};

// Made with Bob
