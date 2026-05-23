<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mou_renewals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mou_id')->constrained()->cascadeOnDelete();
            $table->integer('renewal_number');
            $table->date('old_start_date');
            $table->date('old_end_date');
            $table->date('new_start_date');
            $table->date('new_end_date');
            $table->integer('duration_months')->nullable();
            $table->text('renewal_note')->nullable();
            $table->string('old_file')->nullable();
            $table->string('new_file')->nullable();
            $table->foreignId('renewed_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mou_renewals');
    }
};
