<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('content_forms', function (Blueprint $table) {
            $table->id();

            $table->enum('type', ['review', 'cerita_magang', 'menfess', 'qna'])->default('review');

            $table->string('name');
            // contoh:
            // Review Kantor Default
            // Cerita Magang Default
            // Menfess Default

            $table->json('schema');

            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('version')->default(1);

            $table->timestamps();

            $table->index('type');
            $table->index('is_active');
            $table->index(['type', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('content_forms');
    }
};
