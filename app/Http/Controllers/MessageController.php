<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    /**
     * Display the home view.
     *
     * @return \Illuminate\View\View
     */
    public function createMessage(Request $request)
    {
        try {
            Message::create([
                'comment' => $request->input('message'),
                'user_id' => Auth::id()
            ]);
        } catch (\Exception $exception) {
            return back()->with(['error' => 'Unable to save your message']);
        }

        return redirect('/');
    }

    public function createAdminReply(Request $request)
    {
        if (Auth::user()->is_admin == true)
        {
            $message = Message::firstWhere('id', '=', $request->route()->parameter('message_id'));

            try {
                $message->reply = $request->input('reply');
                $message->save();
            } catch (\Exception $exception) {
                return back()->with(['error' => 'Unable to save your message']);
            }
        }

        return redirect('/');
    }

    public function deleteMessage(Request $request)
    {
        $message = Message::firstWhere('id', '=', $request->route()->parameter('message_id'));

        if (Auth::user()->id == $message->user_id || Auth::user()->is_admin) {
            try {
                $message->delete();
            } catch (\Exception $exception) {
                return back()->with(['error' => 'Unable to save your message']);
            }
        }

        return redirect('/');
    }

    public function editMessage(Request $request)
    {
        $message = Message::firstWhere('id', '=', $request->route()->parameter('message_id'));

        if (Auth::user()->id == $message->user_id || Auth::user()->is_admin) {
            try {
                $message->comment = $request->input('message');
                $message->save();
            } catch (\Exception $exception) {
                return back()->with(['error' => 'Unable to save your message']);
            }
        }

        return redirect('/');
    }

    public function editMessageAdmin(Request $request)
    {
        $message = Message::firstWhere('id', '=', $request->route()->parameter('message_id'));

        if (Auth::user()->is_admin) {
            try {
                $message->reply = $request->input('message');
                $message->save();
            } catch (\Exception $exception) {
                return back()->with(['error' => 'Unable to save your message']);
            }
        }

        return redirect('/');
    }
}