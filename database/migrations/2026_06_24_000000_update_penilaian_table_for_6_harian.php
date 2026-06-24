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
        Schema::table('penilaian', function (Blueprint $table) {
            $table->dropColumn('nilai_tugas');
            $table->decimal('nilai_harian_1', 5, 2)->nullable()->after('mapel_id');
            $table->decimal('nilai_harian_2', 5, 2)->nullable()->after('nilai_harian_1');
            $table->decimal('nilai_harian_3', 5, 2)->nullable()->after('nilai_harian_2');
            $table->decimal('nilai_harian_4', 5, 2)->nullable()->after('nilai_harian_3');
            $table->decimal('nilai_harian_5', 5, 2)->nullable()->after('nilai_harian_4');
            $table->decimal('nilai_harian_6', 5, 2)->nullable()->after('nilai_harian_5');
            $table->decimal('nilai_harian', 5, 2)->nullable()->after('nilai_harian_6');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penilaian', function (Blueprint $table) {
            $table->decimal('nilai_tugas', 5, 2)->nullable()->after('mapel_id');
            $table->dropColumn([
                'nilai_harian_1',
                'nilai_harian_2',
                'nilai_harian_3',
                'nilai_harian_4',
                'nilai_harian_5',
                'nilai_harian_6',
                'nilai_harian'
            ]);
        });
    }
};
