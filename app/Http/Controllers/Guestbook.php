<?php

namespace App\Http\Controllers\Web\Home;

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
        $messages = Message::all();
        return view('guestbook', [
            'messages' => $messages
        ]);
    }
}