<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GuestbookController extends Controller
{
    /**
     * Display the home view.
     *
     * @return \Illuminate\View\View
     */
    public function renderMain()
    {
        $messages = Message::with('user', 'highlight')->orderBy('created_at', 'DESC')->paginate(15);

        return view('guestbook', [
            'messages' => $messages
        ]);
    }

    /**
     * Display the message view.
     *
     * @return \Illuminate\View\View
     */
    public function renderMessage(Request $request)
    {
        $message = Message::firstWhere('id', '=', $request->route()->parameter('message_id'));

        if (Auth::user() && (Auth::user()->id == $message->user_id || Auth::user()->is_admin)) {
            $comments = Comment::with('user')->where('message_id', '=', $message->id)->orderBy('created_at', 'ASC')->paginate(15);

            return view('message', [
                'message' => $message,
                'comments' => $comments
            ]);
        } else {
            return redirect('/');
        };
    }
}
