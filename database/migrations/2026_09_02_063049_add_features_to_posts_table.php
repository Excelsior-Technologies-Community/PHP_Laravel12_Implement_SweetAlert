<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->text('content')->nullable()->after('title');
            $table->string('category')->nullable()->after('content');
            $table->string('image')->nullable()->after('category');
            $table->enum('status', ['published', 'draft'])->default('published')->after('image');
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn(['content', 'category', 'image', 'status']);
            $table->dropSoftDeletes();
        });
    }
};
