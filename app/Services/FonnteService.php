<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class FonnteService
{
    /**
     * Kirim pesan WhatsApp menggunakan Fonnte.
     *
     * @param string $target Nomor telepon (atau dipisahkan koma untuk banyak nomor)
     * @param string $message Isi pesan
     * @return array Respon dari Fonnte
     */
    public static function send($target, $message)
    {
        $token = env('FONNTE_TOKEN');

        // Format nomor agar diawali 62
        $target = self::formatPhoneNumber($target);

        Log::info("WA NOTIFICATION TRIGGERED: To: {$target}, Msg: '{$message}'");

        if (empty($token) || $token === 'YOUR_FONNTE_TOKEN_HERE') {
            Log::warning("Fonnte Token belum diisi di .env. Pesan dicatat di log sistem.");
            return [
                'status' => false,
                'message' => 'Fonnte token tidak terkonfigurasi. Notifikasi berhasil disimulasikan dan dicatat di log sistem.',
                'simulated' => true
            ];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $token
            ])->post('https://api.fonnte.com/send', [
                'target' => $target,
                'message' => $message
            ]);

            $result = $response->json();

            Log::info("Fonnte API Response:", ['result' => $result]);

            return [
                'status' => $result['status'] ?? false,
                'message' => $result['reason'] ?? ($result['message'] ?? 'Permintaan Fonnte dikirim'),
                'raw' => $result
            ];
        } catch (\Exception $e) {
            Log::error("Fonnte API Error: " . $e->getMessage());
            return [
                'status' => false,
                'message' => 'Gagal mengirim pesan via Fonnte: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Memformat nomor telepon Indonesia agar sesuai format internasional 62
     */
    private static function formatPhoneNumber($number)
    {
        $number = preg_replace('/[^0-9,]/', '', $number); // Bersihkan selain angka dan koma
        $numbers = explode(',', $number);

        foreach ($numbers as &$num) {
            $num = trim($num);
            if (str_starts_with($num, '08')) {
                $num = '628' . substr($num, 2);
            } elseif (str_starts_with($num, '8')) {
                $num = '628' . substr($num, 1);
            }
        }

        return implode(',', $numbers);
    }
}
