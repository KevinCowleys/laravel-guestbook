<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class MessageController extends Controller
{
    /**
     * Function that will create a message
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function createMessage(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required',
        ]);

        if ($validator->fails()) {
            return back()->withErrors(['error' => 'Unable to save your message']);
        }

        Message::create([
            'comment' => $request->input('message'),
            'user_id' => Auth::id()
        ]);

        return redirect('/');
    }

    /**
     * Function that will edit messages if made by the user that owns it
     * or if an admin actioned it
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function editMessage(Request $request): RedirectResponse
    {
        $errorResponse = ['error' => 'Unable to update your message'];

        $validator = Validator::make($request->all(), [
            'message' => 'required',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($errorResponse);
        }

        $message = Message::firstWhere('id', '=', $request->route()->parameter('message_id'));

        // Add where if user isn't Admin
        if (!Auth::user()->is_admin) {
            $message->where('user_id', '=', Auth::user()->id);
        }

        // Don't allow other users to edit
        if (Auth::user()->id !== $message->user_id && !Auth::user()->is_admin) {
            return back()->withErrors($errorResponse);
        }

        try {
            $message->update(['comment' =>  $request->input('message')]);
        } catch (\Exception $exception) {
            return back()->withErrors($errorResponse);
        }

        return redirect('/');
    }

    /**
     * Function that will delete a comment if the comment is owned by the user
     * or if an admin actioned the delete
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function deleteMessage(Request $request): RedirectResponse
    {
        $message = Message::where('id', '=', $request->route()->parameter('message_id'));

        // This check stops normal users from deleting
        // each others work
        if (!Auth::user()->is_admin) {
            $message->where('user_id', '=', Auth::user()->id);
        }

        try {
            $message->limit(1)->delete();
        } catch (\Exception $exception) {
            return back()->withErrors(['error' => 'Unable to delete your message']);
        }

        return redirect('/');
    }
}
