<div class="flex justify-start items-center text-xs w-full">
    <div class="font-semibold text-gray-700 flex items-center justify-center space-x-1 dark:text-gray-400">
        @if (!$reply)
            <button onClick="toggleReply({{ $message->id }})" class="hover:underline">Reply</button>
            <small class="self-center">.</small>
            <button onClick="toggleEdit({{ $message->id }})" class="hover:underline">Edit</button>
            <small class="self-center">.</small>
            <form action="/message/{{ $message->id }}/delete" method="post">
                @csrf
                <input type="submit" value="Delete" class="hover:underline cursor-pointer" onclick="return confirm('Are you sure you want to delete this item?');">
            </form>
        @else
            @if (Auth::user()->is_admin)
                <form method="POST" action="/message/{{ $message_id }}/{{ $message->id }}/star">
                    @csrf
                    <a href="/message/{{ $message_id }}/{{ $message->id }}/star" class="text-sm text-gray-700 dark:text-gray-500 hover:underline" onclick="event.preventDefault(); this.closest('form').submit();">
                        @if ($message->favourite == true)
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                            </svg>
                        @endif
                    </a>
                </form>
            @endif
            <button onClick="toggleEdit({{ $message->id }})" class="hover:underline">Edit</button>
            <small class="self-center">.</small>
            <form action="/message/{{ $message_id }}/{{ $message->id }}/delete" method="post">
                @csrf
                <input type="submit" value="Delete" class="hover:underline cursor-pointer" onclick="return confirm('Are you sure you want to delete this item?');">
            </form>
        @endif
    </div>
</div>

@if (!$reply)
    @include('includes.comment-box', [
        'url' => "/message/$message->id/reply",
        'name' => 'message',
        'id' => "$message->id-reply",
        'hide' => true,
    ])
    @include('includes.comment-box', [
        'url' => "/message/$message->id/edit",
        'name' => 'message',
        'id' => "$message->id-edit",
        'comment' => $message->comment,
        'hide' => true,
    ])
@else
    @include('includes.comment-box', [
        'url' => "/message/$message_id/$message->id/edit",
        'name' => 'message',
        'id' => "$message->id-edit",
        'comment' => $message->comment,
        'hide' => true,
    ])
@endif
