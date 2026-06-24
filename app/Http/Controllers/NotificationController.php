<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Display a listing of all notifications for the authenticated user.
     */
    public function index()
    {
        $notifications = auth()->user()->appNotifications()->latest()->paginate(10);
        
        return view('notifications.index', compact('notifications'));
    }

    /**
     * Mark a specific notification as read and redirect to its link if available.
     */
    public function markAsRead($id)
    {
        $notification = AppNotification::where('user_id', auth()->id())->findOrFail($id);
        $notification->update(['is_read' => true]);

        if ($notification->link) {
            return redirect($notification->link);
        }

        return redirect()->back()->with('success', 'Notifikasi ditandai telah dibaca.');
    }

    /**
     * Mark all notifications of the authenticated user as read.
     */
    public function markAllAsRead()
    {
        auth()->user()->unreadAppNotifications()->update(['is_read' => true]);

        return redirect()->back()->with('success', 'Semua notifikasi ditandai telah dibaca.');
    }
}
