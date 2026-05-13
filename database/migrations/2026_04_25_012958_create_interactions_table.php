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

            // Self reference untuk reply
            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('interactions')
                ->cascadeOnDelete();

            $table->foreignId('office_id')
                ->constrained('offices')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->json('attributes')->nullable();

            $table->enum('type', ['review', 'cerita_magang', 'menfess', 'qna'])
                ->default('review');

            $table->boolean('is_anonymous')->default(false);
            $table->boolean('is_hidden')->default(false);
            $table->timestamp('reported_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('ulid');
            $table->index('parent_id');
            $table->index('office_id');
            $table->index('user_id');
            $table->index('type');
            $table->index('is_hidden');
            $table->index(['office_id', 'is_hidden']);
            $table->index(['parent_id', 'is_hidden']);
            $table->index(['office_id', 'type', 'is_hidden']);
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
