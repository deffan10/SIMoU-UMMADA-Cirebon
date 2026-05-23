<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mous', function (Blueprint $table) {
            $table->id();
            $table->string('mou_number')->unique();
            $table->string('title');
            $table->string('slug')->unique();
            $table->foreignId('institution_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('level', ['lokal', 'nasional', 'internasional'])->default('nasional');
            $table->enum('type', ['akademik', 'penelitian', 'mbkm', 'industri', 'pengabdian', 'pemerintah', 'internasional'])->default('akademik');
            $table->enum('cooperation_type', ['mou', 'moa', 'ia', 'pks', 'lainnya'])->default('mou');
            $table->foreignId('faculty_id')->nullable()->constrained()->nullOnDelete();
            $table->string('study_program')->nullable();
            $table->string('pic_name')->nullable();
            $table->string('pic_phone')->nullable();
            $table->string('pic_email')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('duration_months')->nullable();
            $table->enum('status', ['aktif', 'akan_expire', 'expire'])->default('aktif');
            $table->enum('visibility', ['public', 'internal'])->default('internal');
            $table->text('description')->nullable();
            $table->text('public_summary')->nullable();
            $table->string('main_document')->nullable();
            $table->boolean('show_pdf_public')->default(false);
            $table->boolean('allow_download')->default(false);
            $table->integer('renewal_count')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'visibility']);
            $table->index(['start_date', 'end_date']);
            $table->index('level');
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mous');
    }
};
