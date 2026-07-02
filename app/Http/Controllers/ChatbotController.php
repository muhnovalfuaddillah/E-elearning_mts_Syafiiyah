<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatbotController extends Controller
{
    /**
     * Handle the chat request.
     */
    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $userMessage = $request->input('message');
        $user = auth()->user();

        // Get Gemini API Key
        $apiKey = env('GEMINI_API_KEY');
        if (empty($apiKey)) {
            return response()->json([
                'success' => false,
                'message' => 'Konfigurasi API Key Gemini belum lengkap. Silakan minta Admin untuk menambahkan `GEMINI_API_KEY` di file `.env` aplikasi.',
            ], 400);
        }

        // Get database statistics
        $siswaCount = \App\Models\Siswa::count();
        $guruCount = \App\Models\User::where('role', 'guru')->count();
        $kelasCount = Kelas::count();
        $mapelCount = \App\Models\MataPelajaran::count();

        // Get recent announcements
        $announcements = \App\Models\Pengumuman::latest()->take(3)->get()->map(function ($p) {
            return "- [{$p->created_at->format('d M Y')}] **{$p->judul}**: " . strip_tags($p->isi);
        })->implode("\n");
        if (empty($announcements)) {
            $announcements = "Tidak ada pengumuman aktif saat ini.";
        }

        // Get registered classes in database to educate the AI
        $classes = Kelas::all()->map(function ($kelas) {
            return "- {$kelas->kode_kelas} ({$kelas->nama_kelas} - Tingkat {$kelas->tingkat})";
        })->implode("\n");

        // Heuristic Dynamic Context queries
        $dynamicContext = "";
        $lowerMsg = strtolower($userMessage);

        // 1. Detect query about students inside a class
        if (str_contains($lowerMsg, 'siswa') && (str_contains($lowerMsg, 'kelas') || str_contains($lowerMsg, 'daftar') || str_contains($lowerMsg, 'siapa'))) {
            $kelasModelList = Kelas::all();
            foreach ($kelasModelList as $k) {
                // If message contains class code (e.g. KLS-10-MIPA1) or class name (e.g. MIPA 1)
                if (str_contains($lowerMsg, strtolower($k->kode_kelas)) || str_contains($lowerMsg, strtolower($k->nama_kelas))) {
                    $siswaInClass = \App\Models\Siswa::where('kelas_id', $k->id)->get();
                    if ($siswaInClass->isNotEmpty()) {
                        $siswaNames = $siswaInClass->map(function ($s) {
                            return "{$s->nama} (NIS: {$s->nis})";
                        })->implode(', ');
                        $dynamicContext .= "\n[DATA REALTIME] Daftar siswa di kelas {$k->nama_kelas} ({$k->kode_kelas}): {$siswaNames}.\n";
                    } else {
                        $dynamicContext .= "\n[DATA REALTIME] Tidak ada siswa terdaftar di kelas {$k->nama_kelas} ({$k->kode_kelas}) saat ini.\n";
                    }
                }
            }
        }

        // 2. Detect query about teachers (guru)
        if (str_contains($lowerMsg, 'guru') && (str_contains($lowerMsg, 'daftar') || str_contains($lowerMsg, 'siapa') || str_contains($lowerMsg, 'list'))) {
            $gurus = \App\Models\User::where('role', 'guru')->get();
            if ($gurus->isNotEmpty()) {
                $guruNames = $gurus->map(function ($g) {
                    return "- {$g->name} (NIP: " . ($g->nip ?? '-') . ", Mapel: " . ($g->mapel ?? '-') . ")";
                })->implode("\n");
                $dynamicContext .= "\n[DATA REALTIME] Daftar Guru aktif saat ini:\n{$guruNames}\n";
            }
        }

        // 3. Detect query about schedule (jadwal)
        if (str_contains($lowerMsg, 'jadwal')) {
            $schedules = \App\Models\JadwalPelajaran::with(['kelas', 'mataPelajaran', 'guru'])->get();
            $matchedSchedules = [];
            
            // Check if user specified a class name or day in their message
            $days = ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu', 'minggu'];
            $specifiedDay = null;
            foreach ($days as $day) {
                if (str_contains($lowerMsg, $day)) {
                    $specifiedDay = ucfirst($day);
                }
            }

            foreach ($schedules as $j) {
                $kelasName = strtolower($j->kelas->nama_kelas ?? '');
                $kelasCode = strtolower($j->kelas->kode_kelas ?? '');
                
                $isClassMatch = str_contains($lowerMsg, $kelasName) || str_contains($lowerMsg, $kelasCode);
                $isDayMatch = $specifiedDay ? ($j->hari === $specifiedDay) : true;

                // If class matches (or no specific class is mentioned in the query), and day matches
                if (($isClassMatch && $isDayMatch) || ($specifiedDay && $j->hari === $specifiedDay && !$isClassMatch)) {
                    $matchedSchedules[] = "- **Hari {$j->hari}**: Kelas " . ($j->kelas->nama_kelas ?? '-') . " -> Matapelajaran: " . ($j->mataPelajaran->nama_mapel ?? '-') . " (Guru: " . ($j->guru->name ?? '-') . ", Jam: {$j->jam_mulai} - {$j->jam_selesai})";
                }
            }

            if (!empty($matchedSchedules)) {
                $scheduleList = implode("\n", array_slice($matchedSchedules, 0, 10));
                $dynamicContext .= "\n[DATA REALTIME] Jadwal Pelajaran relevan:\n{$scheduleList}\n";
            }
        }

        // Construct System Instruction
        $systemInstruction = "Anda adalah 'Asisten AI Pembelajaran Digital' (MTs Syafiiyah). Tugas Anda adalah membantu Guru, Siswa, dan Admin dalam menggunakan sistem e-learning ini.\n\n"
            . "DETAIL PENGGUNA SAAT INI:\n"
            . "- Nama: {$user->name}\n"
            . "- Role: {$user->role}\n\n"
            . "STATISTIK APLIKASI SAAT INI:\n"
            . "- Total Siswa Aktif: {$siswaCount}\n"
            . "- Total Guru Aktif: {$guruCount}\n"
            . "- Total Kelas: {$kelasCount}\n"
            . "- Total Mata Pelajaran: {$mapelCount}\n\n"
            . "PENGUMUMAN TERBARU SEKOLAH:\n"
            . "{$announcements}\n\n"
            . "DATA KELAS TERDAFTAR:\n"
            . "{$classes}\n\n"
            . $dynamicContext
            . "INSTRUKSI PENTING:\n"
            . "1. Jawablah pertanyaan seputar jumlah siswa, guru, kelas, pengumuman terbaru, jadwal pelajaran, atau daftar siswa menggunakan [DATA REALTIME] atau [STATISTIK APLIKASI] yang disediakan di atas.\n"
            . "2. Jika pengguna bertanya tentang ERROR IMPORT EXCEL/CSV (misalnya kelas tidak terdaftar seperti 'X-RPL-1'), arahkan mereka untuk menggunakan kode kelas yang valid (contoh: 'KLS-10-MIPA1').\n"
            . "3. Jawablah menggunakan Bahasa Indonesia yang ramah, sopan, komunikatif, dan ringkas.\n"
            . "4. Gunakan pemformatan Markdown (bold, list, bullet) agar pesan rapi.";

        // Retrieve or initialize conversation history from session
        $history = session()->get('chatbot_history', []);

        // Construct contents and request body based on API version
        // Standardized for 2026 available models based on active API key permissions
        $models = [
            ['version' => 'v1beta', 'name' => 'gemini-2.5-flash'],
            ['version' => 'v1', 'name' => 'gemini-2.5-flash'],
            ['version' => 'v1beta', 'name' => 'gemini-2.5-flash-lite'],
            ['version' => 'v1', 'name' => 'gemini-2.5-flash-lite'],
            ['version' => 'v1beta', 'name' => 'gemini-3.1-flash-lite'],
            ['version' => 'v1beta', 'name' => 'gemini-3.5-flash'],
            ['version' => 'v1beta', 'name' => 'gemini-2.0-flash-lite'],
            ['version' => 'v1beta', 'name' => 'gemini-2.0-flash'],
            ['version' => 'v1beta', 'name' => 'gemini-2.5-pro'],
        ];

        $aiResponse = null;
        $errors = [];

        foreach ($models as $modelConfig) {
            $version = $modelConfig['version'];
            $modelName = $modelConfig['name'];
            $url = "https://generativelanguage.googleapis.com/{$version}/models/{$modelName}:generateContent?key={$apiKey}";

            $geminiContents = [];
            $isFirst = true;

            if ($version === 'v1') {
                // For v1, prepend system instruction to the first user message
                foreach ($history as $chat) {
                    $text = $chat['text'];
                    if ($isFirst && $chat['role'] === 'user') {
                        $text = "[INSTRUKSI SISTEM]\n{$systemInstruction}\n\n[PESAN USER]\n{$text}";
                        $isFirst = false;
                    }
                    $geminiContents[] = [
                        'role' => $chat['role'] === 'user' ? 'user' : 'model',
                        'parts' => [
                            ['text' => $text]
                        ]
                    ];
                }

                $currentUserMessageText = $userMessage;
                if ($isFirst) {
                    $currentUserMessageText = "[INSTRUKSI SISTEM]\n{$systemInstruction}\n\n[PESAN USER]\n{$userMessage}";
                }
                $geminiContents[] = [
                    'role' => 'user',
                    'parts' => [
                        ['text' => $currentUserMessageText]
                    ]
                ];

                $body = [
                    'contents' => $geminiContents,
                    'generationConfig' => [
                        'temperature' => 0.7,
                        'maxOutputTokens' => 800,
                    ]
                ];
            } else {
                // For v1beta, use native systemInstruction field
                foreach ($history as $chat) {
                    $geminiContents[] = [
                        'role' => $chat['role'] === 'user' ? 'user' : 'model',
                        'parts' => [
                            ['text' => $chat['text']]
                        ]
                    ];
                }
                $geminiContents[] = [
                    'role' => 'user',
                    'parts' => [
                        ['text' => $userMessage]
                    ]
                ];

                $body = [
                    'contents' => $geminiContents,
                    'systemInstruction' => [
                        'parts' => [
                            ['text' => $systemInstruction]
                        ]
                    ],
                    'generationConfig' => [
                        'temperature' => 0.7,
                        'maxOutputTokens' => 800,
                    ]
                ];
            }

            try {
                $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                ])->withOptions([
                    'verify' => false,
                    'force_ip_resolve' => 'v4',
                    'timeout' => 12,
                ])->post($url, $body);

                if ($response->successful()) {
                    $result = $response->json();
                    $aiResponse = $result['candidates'][0]['content']['parts'][0]['text'] ?? null;
                    if ($aiResponse) {
                        break; // Success, stop trying other models
                    }
                } else {
                    $errorData = $response->json();
                    $lastError = $errorData['error']['message'] ?? "Error {$response->status()}";
                    $errors[] = "{$modelName}({$version}): {$lastError}";
                    Log::warning("Gemini model {$modelName} ({$version}) failed: {$lastError}");
                }
            } catch (\Exception $e) {
                $lastError = $e->getMessage();
                $errors[] = "{$modelName}({$version}) exception: {$lastError}";
                Log::warning("Gemini model {$modelName} ({$version}) exception: {$lastError}");
            }
        }

        if ($aiResponse) {
            // Save user message and AI response to session history
            $history[] = ['role' => 'user', 'text' => $userMessage];
            $history[] = ['role' => 'model', 'text' => $aiResponse];
            
            if (count($history) > 30) {
                $history = array_slice($history, -30);
            }
            session()->put('chatbot_history', $history);

            return response()->json([
                'success' => true,
                'message' => $aiResponse,
            ]);
        } else {
            $combinedError = implode(' | ', $errors);
            return response()->json([
                'success' => false,
                'message' => 'Asisten AI sedang sibuk atau mengalami kendala teknis. Detail: ' . $combinedError,
            ], 500);
        }
    }

    /**
     * Clear chat history.
     */
    public function clear()
    {
        session()->forget('chatbot_history');
        return response()->json([
            'success' => true,
            'message' => 'Riwayat percakapan berhasil dihapus.',
        ]);
    }
}
