<?php

namespace App\Http\Controllers;

use App\Models\AcademicCalendar;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class AcademicCalendarController extends Controller
{
    /**
     * Menampilkan daftar agenda akademik (Untuk Admin).
     */
    public function index(Request $request)
    {
        $events = AcademicCalendar::orderBy('start_date', 'asc')->get();
        return view('admin.calendar.index', compact('events'));
    }

    /**
     * Endpoint API JSON untuk data kalender (FullCalendar / render JS).
     */
    public function getEventsJson()
    {
        $events = AcademicCalendar::all();
        $formatted = $events->map(function($event) {
            $colors = [
                'libur' => '#ef4444',     // Merah
                'ujian' => '#eab308',     // Kuning
                'kegiatan' => '#3b82f6',  // Biru
                'umum' => '#a855f7',      // Ungu
            ];
            return [
                'id' => $event->id,
                'title' => $event->title,
                'start' => $event->start_date,
                'end' => $event->end_date ?? $event->start_date,
                'color' => $colors[$event->type] ?? '#a855f7',
                'description' => $event->description,
                'type' => $event->type
            ];
        });
        return response()->json($formatted);
    }

    /**
     * Menyimpan agenda akademik baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:100',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'type' => 'required|in:libur,ujian,kegiatan,umum',
            'description' => 'nullable|string',
        ]);

        AcademicCalendar::create($request->all());

        ActivityLog::log('create_calendar_event', 'Menambahkan agenda akademik: ' . $request->title);

        return redirect()->back()->with('success', 'Agenda akademik berhasil ditambahkan!');
    }

    /**
     * Memperbarui agenda akademik.
     */
    public function update(Request $request, $id)
    {
        $event = AcademicCalendar::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:100',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'type' => 'required|in:libur,ujian,kegiatan,umum',
            'description' => 'nullable|string',
        ]);

        $event->update($request->all());

        ActivityLog::log('update_calendar_event', 'Memperbarui agenda akademik: ' . $request->title);

        return redirect()->back()->with('success', 'Agenda akademik berhasil diperbarui!');
    }

    /**
     * Menghapus agenda akademik.
     */
    public function destroy($id)
    {
        $event = AcademicCalendar::findOrFail($id);
        $title = $event->title;
        $event->delete();

        ActivityLog::log('delete_calendar_event', 'Menghapus agenda akademik: ' . $title);

        return redirect()->back()->with('success', 'Agenda akademik berhasil dihapus!');
    }
}
