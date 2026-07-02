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
        // 1. Tabel Ujian
        Schema::create('ujians', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->text('deskripsi')->nullable();
            $table->foreignId('kelas_id')->constrained('kelas')->onDelete('cascade');
            $table->foreignId('mapel_id')->constrained('mata_pelajaran')->onDelete('cascade');
            $table->foreignId('guru_id')->constrained('users')->onDelete('cascade');
            $table->dateTime('waktu_mulai');
            $table->dateTime('waktu_selesai');
            $table->integer('durasi'); // dalam menit
            $table->enum('status', ['draft', 'published', 'closed'])->default('draft');
            $table->timestamps();
        });

        // 2. Tabel Soal Ujian
        Schema::create('ujian_soals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ujian_id')->constrained('ujians')->onDelete('cascade');
            $table->text('pertanyaan');
            $table->text('opsi_a');
            $table->text('opsi_b');
            $table->text('opsi_c');
            $table->text('opsi_d');
            $table->text('opsi_e')->nullable();
            $table->char('kunci_jawaban', 1); // A, B, C, D, E
            $table->timestamps();
        });

        // 3. Tabel Sesi Ujian Siswa
        Schema::create('ujian_siswas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ujian_id')->constrained('ujians')->onDelete('cascade');
            $table->foreignId('siswa_id')->constrained('siswa')->onDelete('cascade');
            $table->decimal('nilai', 5, 2)->nullable();
            $table->dateTime('waktu_mulai');
            $table->dateTime('waktu_selesai')->nullable();
            $table->enum('status', ['mengerjakan', 'selesai'])->default('mengerjakan');
            $table->timestamps();
        });

        // 4. Tabel Jawaban Siswa
        Schema::create('ujian_jawabans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ujian_siswa_id')->constrained('ujian_siswas')->onDelete('cascade');
            $table->foreignId('ujian_soal_id')->constrained('ujian_soals')->onDelete('cascade');
            $table->char('jawaban', 1)->nullable(); // A, B, C, D, E
            $table->boolean('is_benar')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ujian_jawabans');
        Schema::dropIfExists('ujian_siswas');
        Schema::dropIfExists('ujian_soals');
        Schema::dropIfExists('ujians');
    }
};
