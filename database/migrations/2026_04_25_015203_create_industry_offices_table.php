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
        Schema::create('industry_office', function (Blueprint $table) {
            $table->id();

            $table->foreignId('office_id')
                ->constrained('offices')
                ->cascadeOnDelete();

            $table->foreignId('industry_id')
                ->constrained('industries')
                ->cascadeOnDelete();

            $table->timestamps();

            $table->unique(['office_id', 'industry_id']);

            $table->index('office_id');
            $table->index('industry_id');
            $table->index(['industry_id', 'office_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('industry_offices');
    }
};
