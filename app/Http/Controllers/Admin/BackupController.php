<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\ActivityLog;

class BackupController extends Controller
{
    /**
     * Mengekspor skema dan data database ke file .sql untuk diunduh.
     */
    public function backup()
    {
        // Catat aktivitas
        ActivityLog::log('backup_database', 'Administrator mengunduh backup database sekolah.');

        $driver = DB::getDriverName();
        $sqlDump = "-- Pembelajaran Digital Database Backup\n";
        $sqlDump .= "-- Dibuat pada: " . date('Y-m-d H:i:s') . "\n";
        $sqlDump .= "-- Database Driver: " . $driver . "\n\n";

        if ($driver === 'mysql') {
            $tables = DB::select('SHOW TABLES');

            foreach ($tables as $table) {
                $tableArray = (array)$table;
                $tableName = reset($tableArray);
                
                // Ambil query pembuatan tabel
                $createTable = DB::select("SHOW CREATE TABLE `{$tableName}`");
                $sqlDump .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
                $sqlDump .= $createTable[0]->{'Create Table'} . ";\n\n";

                // Ambil data baris
                $rows = DB::table($tableName)->get();
                foreach ($rows as $row) {
                    $rowArray = (array)$row;
                    $columns = array_keys($rowArray);
                    $values = array_map(function($value) {
                        if (is_null($value)) return 'NULL';
                        return DB::getPdo()->quote($value);
                    }, array_values($rowArray));

                    $sqlDump .= "INSERT INTO `{$tableName}` (`" . implode("`, `", $columns) . "`) VALUES (" . implode(", ", $values) . ");\n";
                }
                $sqlDump .= "\n\n";
            }
        } elseif ($driver === 'sqlite') {
            $tables = DB::select("SELECT name, sql FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%';");
            foreach ($tables as $table) {
                $tableName = $table->name;
                $sqlDump .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
                $sqlDump .= $table->sql . ";\n\n";

                $rows = DB::table($tableName)->get();
                foreach ($rows as $row) {
                    $rowArray = (array)$row;
                    $columns = array_keys($rowArray);
                    $values = array_map(function($value) {
                        if (is_null($value)) return 'NULL';
                        return DB::getPdo()->quote($value);
                    }, array_values($rowArray));

                    $sqlDump .= "INSERT INTO `{$tableName}` (`" . implode("`, `", $columns) . "`) VALUES (" . implode(", ", $values) . ");\n";
                }
                $sqlDump .= "\n\n";
            }
        } else {
            return redirect()->back()->with('error', 'Koneksi database tidak didukung untuk backup otomatis.');
        }

        $fileName = 'backup_pembelajaran_digital_' . date('Ymd_His') . '.sql';

        return response($sqlDump, 200, [
            'Content-Type' => 'application/sql',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }
}
