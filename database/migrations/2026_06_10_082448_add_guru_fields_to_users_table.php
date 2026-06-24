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
        Schema::table('users', function (Blueprint $table) {
            $table->string('nip')->nullable()->unique()->after('email');
            $table->string('mapel')->nullable()->after('nip');
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable()->after('mapel');
            $table->string('telp')->nullable()->after('jenis_kelamin');
            $table->text('alamat')->nullable()->after('telp');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['nip', 'mapel', 'jenis_kelamin', 'telp', 'alamat']);
        });
    }
};
