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
        Schema::create('peminjaman_alats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade'); // siapa yang meminjam
            $table->timestamps();
            $table->date('tanggal_pinjam')->nullable();
            // $table->string('status')->nullable();
            $table->string('approval')->nullable();
            $table->text('keterangan')->nullable();
            // $table->integer('jumlah_pinjam')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peminjaman_alats');
    }
};
