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
   Schema::create('kelas', function (Blueprint $table) {
            $table->id();
            $table->string('kode_kelas', 20)->unique();
            $table->string('nama_kelas', 100);
            $table->enum('tingkat', ['7', '8', '9', '10', '11', '12']);
            $table->string('jurusan', 50);
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kelas');
    }
};
