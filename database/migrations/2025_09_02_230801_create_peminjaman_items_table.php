<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('peminjaman_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peminjaman_id')
                ->constrained('peminjaman_alats')
                ->cascadeOnDelete();
            $table->foreignId('peralatan_id')
                ->constrained('peralatans')
                ->cascadeOnDelete();
            $table->unsignedInteger('jumlah')->default(1);
            $table->timestamps();

            // Cegah alat ganda di satu peminjaman
            $table->unique(['peminjaman_id', 'peralatan_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peminjaman_items');
    }
};
