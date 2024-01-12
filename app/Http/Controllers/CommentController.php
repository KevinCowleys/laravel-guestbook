<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function createReply(Request $request)
    {
        $message = Message::firstWhere('id', '=', $request->route()->parameter('message_id'));

        if (Auth::user()->id == $message->user_id || Auth::user()->is_admin) {
            try {
                Comment::create([
                    'comment' => $request->input('message'),
                    'user_id' => Auth::id(),
                    'message_id' => $request->route()->parameter('message_id')
                ]);
            } catch (\Exception $exception) {
                return back()->with(['error' => 'Unable to save your comment']);
            }
        }

        return $this->redirectComment($message->id);
    }

    public function editComment(Request $request)
    {
        $message_id = $request->route()->parameter('message_id');

        $comment = Comment::where('id', '=', $request->route()->parameter('comment_id'))
            ->where('message_id', '=', $message_id)
            ->first();

        if (Auth::user()->id == $comment->user_id || Auth::user()->is_admin) {
            try {
                $comment->comment = $request->input('message');
                $comment->save();
            } catch (\Exception $exception) {
                return back()->with(['error' => 'Unable to edit your comment']);
            }
        }

        return $this->redirectComment($message_id);
    }

    public function toggleFavourite(Request $request)
    {
        if (Auth::user()->is_admin) {
            $message_id = $request->route()->parameter('message_id');

            $comments = Comment::where('message_id', '=', $message_id)->get();

            foreach ($comments as $comment) {
                if ($comment->id == $request->route()->parameter('comment_id')) {
                    $comment->favourite = !$comment->favourite;
                } else {
                    $comment->favourite = false;
                }

                $comment->save();
            }
        }

        return $this->redirectComment($message_id);
    }

    public function deleteComment(Request $request)
    {
        $message_id = $request->route()->parameter('message_id');

        $comment = Comment::where('id', '=', $request->route()->parameter('comment_id'))
            ->where('message_id', '=', $message_id)
            ->first();

        if (Auth::user()->id == $comment->user_id || Auth::user()->is_admin) {
            try {
                $comment->delete();
            } catch (\Exception $exception) {
                return back()->with(['error' => 'Unable to delete your comment']);
            }
        }

        return $this->redirectComment($message_id);
    }

    /**
     * 
     */
    public function redirectComment(int $message_id)
    {
        if (url()->previous() == route('message.show', ['message_id' => $message_id])) {
            return redirect("/message/{$message_id}");
        } else {
            return redirect('/');
        }
    }
}
