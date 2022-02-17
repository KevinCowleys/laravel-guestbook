<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Message;

class GuestbookController extends Controller
{
    /**
     * Display the home view.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        $messages = Message::with('user')->orderBy('created_at', 'DESC')->get();

        return view('guestbook', [
            'messages' => $messages
        ]);
    }
}