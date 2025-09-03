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
        Schema::create('pengembalian_alats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peminjaman_id')
                ->constrained('peminjaman_alats')
                ->cascadeOnDelete();

            // relasi ke item tertentu yang dikembalikan
            $table->foreignId('peminjaman_item_id')
                ->constrained('peminjaman_items')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->date('tanggal_kembali')->nullable();
            $table->unsignedInteger('jumlah_dikembalikan')->default(1);
            $table->string('status_kondisi')->nullable(); // baik/rusak/hilang
            $table->enum('approval', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('keterangan')->nullable();
            $table->timestamps();

            // cegah user melaporkan pengembalian ganda pada item yang sama
            $table->unique(['peminjaman_item_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengembalian_alats');
    }
};
