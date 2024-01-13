<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Message;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    /**
     * Creates a comment / reply if the message_id exists and
     * only user of first message and admin are allowed to reply.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function createReply(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required',
        ]);

        if ($validator->fails()) {
            return back()->withErrors(['error' => 'Unable to save your comment']);
        }

        $message = Message::firstWhere('id', '=', $request->route()->parameter('message_id'));

        if (!$message) {
            return back()->withErrors(['error' => 'Message does not exist']);
        }

        if (Auth::user()->id === $message->user_id || Auth::user()->is_admin) {
            Comment::create([
                'comment' => $request->input('message'),
                'user_id' => Auth::id(),
                'message_id' => $request->route()->parameter('message_id')
            ]);
        }

        return $this->redirectComment($message->id);
    }

    /**
     * Edit a comment / reply if the message_id exists and
     * only user of comment and admin are allowed to edit.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function editComment(Request $request): RedirectResponse
    {
        $errorResponse = ['error' => 'Unable to edit your comment'];

        $validator = Validator::make($request->all(), [
            'message' => 'required',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($errorResponse);
        }

        $messageId = $request->route()->parameter('message_id');
        $comment = Comment::where('id', '=', $request->route()->parameter('comment_id'))
            ->where('message_id', '=', $messageId);

        // Add where if user isn't Admin
        if (!Auth::user()->is_admin) {
            $comment->where('user_id', '=', Auth::user()->id);
        }

        $hasUpdate = false;
        try {
            $hasUpdate = $comment->update(['comment' =>  $request->input('message')]);
        } catch (\Exception $exception) {
            return back()->withErrors($errorResponse);
        }

        // Return user to other view if they aren't the original user
        if (!$hasUpdate && !Auth::user()->is_admin) {
            $message = Message::firstWhere('id', '=', $messageId);

            if (Auth::user()->id !== $message->user_id) {
                return back()->withErrors($errorResponse);
            }
        }

        return $this->redirectComment($messageId);
    }

    /**
     * Function allows an admin user to toggle if a comment
     * is favourite or not.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function toggleFavourite(Request $request): RedirectResponse
    {
        $errorResponse = ['error' => 'Unable to star your comment'];
        $messageId = $request->route()->parameter('message_id');
        $commentId = $request->route()->parameter('comment_id');

        // Return if not admin
        if (!Auth::user()->is_admin) {
            return back()->withErrors($errorResponse);
        }

        // Get the comment that we want to toggle
        $comment = Comment::where('message_id', '=', $messageId)
            ->where('id', '=', $commentId)
            ->first();

        // Return if we can't find the comment in the request
        if (!$comment) {
            return back()->withErrors($errorResponse);
        }

        // Set all favourites to false first,
        // except the one we want to change
        Comment::where('id', '!=', $commentId)
            ->where('message_id', '=', $messageId)
            ->update(['favourite' => false]);

        $comment->favourite = !$comment->favourite;
        $comment->save();

        return $this->redirectComment($messageId);
    }

    /**
     * Function allows the admin or user of the comment
     * to delete comments
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function deleteComment(Request $request): RedirectResponse
    {
        $errorResponse = ['error' => 'Unable to delete your comment'];
        $messageId = $request->route()->parameter('message_id');

        $comment = Comment::where('id', '=', $request->route()->parameter('comment_id'))
            ->where('message_id', '=', $messageId);

        // Add where if user isn't Admin
        if (!Auth::user()->is_admin) {
            $comment->where('user_id', '=', Auth::user()->id);
        }

        $hasUpdate = false;
        try {
            $hasUpdate = $comment->delete();
        } catch (\Exception $exception) {
            return back()->withErrors($errorResponse);
        }

        // Return user to other view if they aren't the original user
        if (!$hasUpdate && !Auth::user()->is_admin) {
            $message = Message::firstWhere('id', '=', $messageId);

            if (Auth::user()->id !== $message->user_id) {
                return back()->withErrors($errorResponse);
            }
        }

        return $this->redirectComment($messageId);
    }

    /**
     * Function to redirect to current comment
     *
     * @param int $messageId
     * @return RedirectResponse
     */
    public function redirectComment(int $messageId): RedirectResponse
    {
        if (url()->previous() === route('message.show', ['message_id' => $messageId])) {
            return redirect("/message/{$messageId}");
        } else {
            return redirect('/');
        }
    }
}
