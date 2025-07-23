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
        Schema::create('pencarian_rute', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('lokasi_asal_id')->constrained('lokasi');
            $table->foreignId('lokasi_tujuan_id')->constrained('lokasi');
            $table->json('jalur_rute'); // menyimpan jalur yang dihasilkan
            $table->decimal('total_jarak', 8, 2);
            $table->integer('total_waktu');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pencarian_rute');
    }
};
