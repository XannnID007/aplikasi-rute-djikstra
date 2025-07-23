<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rute', function (Blueprint $table) {
            $table->json('instruksi_arah')->nullable()->after('waktu_tempuh');
            $table->string('landmark')->nullable()->after('instruksi_arah');
            $table->enum('jenis_jalan', ['gang', 'jalan_kecil', 'jalan_utama'])->default('jalan_kecil')->after('landmark');
        });
    }

    public function down(): void
    {
        Schema::table('rute', function (Blueprint $table) {
            $table->dropColumn(['instruksi_arah', 'landmark', 'jenis_jalan']);
        });
    }
};
