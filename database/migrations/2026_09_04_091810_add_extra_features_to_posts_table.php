<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // No database columns are required for the 8 new features.
        // The existing posts table already contains:
        // title, content, category, image, status, deleted_at, timestamps.
    }

    public function down(): void
    {
        //
    }
};
