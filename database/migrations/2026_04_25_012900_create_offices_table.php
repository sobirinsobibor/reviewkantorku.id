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
        Schema::create('offices', function (Blueprint $table) {
            $table->id();
            $table->ulid('ulid')->unique();
            // Identitas kantor (fisik)
            $table->string('name');
            $table->string('slug')->unique();

            $table->char('province_id', 2);
            $table->char('regency_id', 4);
            $table->text('address')->nullable();

            // Status review
            $table->enum('status', ['pending', 'approved', 'rejected'])
                ->default('pending');

            // Review decision
            $table->foreignId('reviewed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('reviewed_at')->nullable();
            $table->text('rejection_reason')->nullable();

            // Pengaju
            $table->foreignId('created_by')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->timestamps();
            $table->softDeletes();

            /**
             * INDEXING
             */

            // Untuk filter lokasi
            $table->index('province_id');
            $table->index('regency_id');

            // Untuk filter status (penting banget untuk admin & public)
            $table->index('status');

            // Untuk relasi user
            $table->index('reviewed_by');
            $table->index('created_by');

            // Untuk query gabungan (sering dipakai)
            $table->index(['status', 'province_id']);
            $table->index(['status', 'regency_id']);

            // Untuk soft delete (biar query withTrashed lebih cepat)
            $table->index('deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offices');
    }
};
