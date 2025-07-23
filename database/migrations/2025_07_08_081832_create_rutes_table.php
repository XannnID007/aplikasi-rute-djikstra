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
        Schema::create('rute', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lokasi_asal_id')->constrained('lokasi');
            $table->foreignId('lokasi_tujuan_id')->constrained('lokasi');
            $table->decimal('jarak', 8, 2); // dalam kilometer
            $table->integer('waktu_tempuh'); // dalam menit
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rute');
    }
};
