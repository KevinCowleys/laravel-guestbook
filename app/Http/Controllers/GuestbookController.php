<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Message;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class GuestbookController extends Controller
{
    /**
     * Display the home view.
     *
     * @return View
     */
    public function renderMain(): View
    {
        $messages = Message::with(['user', 'highlight.user', 'firstComment.user'])->orderBy('created_at', 'DESC')->paginate(15);

        return view('guestbook', [
            'messages' => $messages
        ]);
    }

    /**
     * Display the message view or redirect to home if user isn't allowed
     * or message doesn't exist
     *
     * @param Request $request
     * @return View|RedirectResponse
     */
    public function renderMessage(Request $request): View|RedirectResponse
    {
        $message = Message::firstWhere('id', '=', $request->route()->parameter('message_id'));

        // Don't allow users that aren't part of the conversation to join in or
        // users that aren't admin
        if (Auth::user() && (Auth::user()->id === $message->user_id || Auth::user()->is_admin)) {
            $comments = Comment::with('user')->where('message_id', '=', $message->id)->orderBy('created_at', 'ASC')->paginate(15);

            return view('message', [
                'message' => $message,
                'comments' => $comments
            ]);
        }

        return redirect('/');
    }
}
