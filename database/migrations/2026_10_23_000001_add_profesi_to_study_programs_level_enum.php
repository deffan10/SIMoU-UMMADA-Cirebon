<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE study_programs MODIFY COLUMN level ENUM('D3', 'D4', 'S1', 'S2', 'S3', 'Profesi') NOT NULL DEFAULT 'S1'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE study_programs MODIFY COLUMN level ENUM('D3', 'D4', 'S1', 'S2', 'S3') NOT NULL DEFAULT 'S1'");
    }
};
