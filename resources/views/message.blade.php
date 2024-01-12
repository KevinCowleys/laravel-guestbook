<x-app-layout>
    <div class="py-12">
        @auth
        @include('includes.comment-box', ['url' => "/message/$message->id/reply", 'name' => 'message', 'id' => 'addReply', 'hide' => false])
        <script>
            function toggleEdit(number) {
                    let edit = document.getElementById( number + '-edit');
                    if (edit.classList.contains('hidden')) {
                        edit.classList = ""
                    } else {
                        edit.classList = "hidden"
                    }
                }
                function toggleReply(number) {
                    let reply = document.getElementById( number + '-reply');
                    if (reply.classList.contains('hidden')) {
                        reply.classList = ""
                    } else {
                        reply.classList = "hidden"
                    }
                }
        </script>
        @endauth
        <div>
            <div class="flex-1 bg-white border rounded-lg px-4 py-2 sm:px-6 sm:py-4 leading-relaxed shadow dark:bg-zinc-800 dark:border-zinc-900">
                <div>{{ $message->user->name }} - <span class="text-xs">{{ date("d M Y", strtotime($message->updated_at)) }}</span></div>
                {{ $message->comment }}
                <div class="mb-4">
                    @auth
                    @if (Auth::user()->id == $message->user_id || Auth::user()->is_admin)
                    @include('includes.comment-buttons', ['reply' => false, 'message' => $message])
                    @endif
                    @endauth
                </div>
                @foreach ($comments as $comment)
                <div class="flex-1 border bg-gray-100 rounded-lg py-2 sm:px-6 sm:py-4 leading-relaxed shadow dark:bg-zinc-800 dark:border-zinc-900">
                    <div>{{ $comment->user->name }} - <span class="text-xs">{{ date("d M Y", strtotime($comment->updated_at)) }}</span></div>
                    {{ $comment->comment }}
                    <div>
                        @auth
                        @if (Auth::user()->id == $comment->user_id || Auth::user()->is_admin)
                        @include('includes.comment-buttons', ['reply' => true, 'message' => $comment,
                        'message_id' => $message->id])
                        @endif
                        @endauth
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        {{ $comments->links('includes.pagination') }}
    </div>
</x-app-layout>
