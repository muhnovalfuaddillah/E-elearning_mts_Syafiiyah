<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class PrayerScheduleController extends Controller
{
    /**
     * Mengambil jadwal sholat bulanan dan menyimpannya di cache selama 24 jam.
     */
    public function getSchedule(Request $request)
    {
        $kabkota = $request->get('kabkota', 'Kab. Probolinggo');
        $provinsi = $request->get('provinsi', 'Jawa Timur');
        
        $date = Carbon::now();
        $bulan = $date->month;
        $tahun = $date->year;

        $cacheKey = "jadwal_sholat_" . str_replace(' ', '_', strtolower($provinsi)) . "_" . str_replace(' ', '_', strtolower($kabkota)) . "_{$bulan}_{$tahun}";

        $data = Cache::remember($cacheKey, 24 * 60 * 60, function () use ($provinsi, $kabkota, $bulan, $tahun) {
            try {
                $response = Http::post('https://equran.id/api/v2/shalat', [
                    'provinsi' => $provinsi,
                    'kabkota' => $kabkota,
                    'bulan' => $bulan,
                    'tahun' => $tahun,
                ]);

                if ($response->successful()) {
                    return $response->json();
                }
            } catch (\Exception $e) {
                // Return null jika API eksternal mengalami gangguan
            }
            return null;
        });

        if (!$data || !isset($data['data'])) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat jadwal sholat dari API equran.id.'
            ], 500);
        }

        return response()->json([
            'success' => true,
            'data' => $data['data']
        ]);
    }
}
