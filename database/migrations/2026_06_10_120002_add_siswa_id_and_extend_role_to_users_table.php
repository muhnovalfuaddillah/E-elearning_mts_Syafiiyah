<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Ubah enum role untuk menyertakan 'siswa'
        $driverName = DB::getDriverName();
        if ($driverName === 'mysql' || $driverName === 'mariadb') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'guru', 'siswa') NOT NULL");
        }

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('siswa_id')->nullable()->after('role')->constrained('siswa')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['siswa_id']);
            $table->dropColumn('siswa_id');
        });

        $driverName = DB::getDriverName();
        if ($driverName === 'mysql' || $driverName === 'mariadb') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'guru') NOT NULL");
        }
    }
};
