<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $itemPerPage = env('ITEM_PER_PAGE', 20);
        $user = Auth::user();
        $isUser = false;
        if ($user->hasAnyRole(['super-admin', 'admin'])) {
            $notifications = DatabaseNotification::orderBy('created_at', 'desc')->paginate($itemPerPage);
        } else {
            $notifications = $user->notifications()->orderBy('created_at', 'desc')->paginate($itemPerPage);
            $isUser = true;
        }

        return view('notifications.index',  ['notifications' => $notifications, 'isUser' => $isUser]);
    }

    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return redirect($notification->data['url'] ?? '/');
    }
}
