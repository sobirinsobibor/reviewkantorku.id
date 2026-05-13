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
        Schema::create('fileables', function (Blueprint $table) {
            $table->id();

            $table->foreignId('file_id')
                ->constrained('files')
                ->cascadeOnDelete();

            $table->morphs('fileable'); 
            // otomatis bikin:
            // fileable_id (indexed)
            // fileable_type (indexed)
            // + composite index

            $table->string('collection')
                ->default('default');

            $table->timestamps();

            /**
             * INDEXING
             */

            // Untuk query berdasarkan file
            $table->index('file_id');

            // Untuk filter berdasarkan collection (misal: review_photos)
            $table->index('collection');

            // Query gabungan (yang paling sering dipakai)
            $table->index(['fileable_type', 'fileable_id', 'collection']);

            // Optional: reverse lookup (jarang tapi useful)
            $table->index(['file_id', 'collection']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fileables');
    }
};
