<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('absensi', function (Blueprint $table) {
            // Drop foreign key first because the unique index is used to enforce it
            $table->dropForeign(['siswa_id']);
            
            // Drop old unique key constraint
            $table->dropUnique('absensi_siswa_id_tanggal_unique');
            
            // Add new unique key constraint including jadwal_pelajaran_id
            $table->unique(['siswa_id', 'jadwal_pelajaran_id', 'tanggal'], 'absensi_siswa_jadwal_tanggal_unique');
            
            // Re-add the foreign key
            $table->foreign('siswa_id')->references('id')->on('siswa')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('absensi', function (Blueprint $table) {
            $table->dropForeign(['siswa_id']);
            $table->dropUnique('absensi_siswa_jadwal_tanggal_unique');
            $table->unique(['siswa_id', 'tanggal'], 'absensi_siswa_id_tanggal_unique');
            $table->foreign('siswa_id')->references('id')->on('siswa')->onDelete('cascade');
        });
    }
};
