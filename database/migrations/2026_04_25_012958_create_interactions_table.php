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
        Schema::create('interactions', function (Blueprint $table) {
            $table->id();
            $table->ulid('ulid')->unique();

            $table->string('first_parent_id', 26)->nullable();
            $table->string('direct_parent_id', 26)->nullable();

            $table->foreignId('office_id')
                ->constrained('offices')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->json('attributes')->nullable();

            $table->enum('type', ['review', 'cerita_magang', 'menfess', 'qna', 'reply'])
                ->default('review');

            $table->enum('reply_to', ['review', 'cerita_magang', 'menfess', 'qna'])
                ->nullable();

            $table->boolean('is_anonymous')->default(false);
            $table->boolean('is_hidden')->default(false);
            $table->timestamp('reported_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('ulid');
            $table->index('first_parent_id');
            $table->index('direct_parent_id');
            $table->index('office_id');
            $table->index('user_id');
            $table->index('type');
            $table->index('is_hidden');
            $table->index(['office_id', 'is_hidden']);
            $table->index(['first_parent_id', 'is_hidden']);
            $table->index(['office_id', 'type', 'is_hidden']);
        });

        // Tambah foreign key setelah tabel selesai dibuat
        Schema::table('interactions', function (Blueprint $table) {
            $table->foreign('first_parent_id')
                ->references('ulid')
                ->on('interactions')
                ->cascadeOnDelete();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('interactions');
    }
};
