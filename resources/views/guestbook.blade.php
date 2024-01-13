<x-app-layout>
    @include('includes.errors', ['errors' => $errors])
    <div class="py-12">
        @auth
            @include('includes.comment-box', ['url' => '/message', 'name' => 'message', 'id' => 'createMessage', 'hide' => false])
            <script>
                function toggleEdit(number) {
                    let edit = document.getElementById(number + '-edit');
                    edit.classList = (edit.classList.contains('hidden') ? '' : 'hidden');
                }

                function toggleReply(number) {
                    let reply = document.getElementById(number + '-reply');
                    reply.classList = (reply.classList.contains('hidden') ? '' : 'hidden');
                }
            </script>
        @endauth
        @foreach ($messages as $message)
            <div>
                <div class="flex-1 border rounded-lg px-4 py-2 sm:px-6 sm:py-4 leading-relaxed bg-white shadow dark:bg-zinc-800 dark:border-zinc-900">
                    <div>{{ $message->user->name }} - <span class="text-xs">{{ date('d M Y', strtotime($message->updated_at)) }}</span></div>
                    {{ $message->comment }}
                    <div>
                        @auth
                            @if (Auth::user()->id == $message->user_id || Auth::user()->is_admin)
                                @include('includes.comment-buttons', ['reply' => false, 'message' => $message])
                            @endif
                        @endauth
                    </div>
                    @php
                        // Quickly set the comment we're dealing with
                        $highlight = $message->highlight ?? $message->firstComment;
                    @endphp
                    @if (isset($highlight))
                        <div class="flex-1 border bg-gray-100 rounded-lg px-4 py-2 sm:px-6 sm:py-4 leading-relaxed bg-white shadow mt-4 dark:bg-zinc-800 dark:border-zinc-900">
                            <div>{{ $highlight->user->name }} - <span class="text-xs">{{ date('d M Y', strtotime($message->updated_at)) }}</span>
                            </div>
                            {{ $highlight->comment }}
                            <div>
                                @auth
                                    @if (Auth::user()->id == $highlight->user_id || Auth::user()->is_admin)
                                        @include('includes.comment-buttons', ['reply' => true, 'message' => $highlight, 'message_id' => $message->id])
                                    @endif
                                @endauth
                            </div>
                        </div>
                        @auth
                            @if (Auth::user()->id == $message->user_id || Auth::user()->is_admin)
                                <a href="/message/{{ $message->id }}" class="block hover:underline cursor-pointer mt-4">Load more</a>
                            @endif
                        @endauth
                    @endif
                </div>
            </div>
        @endforeach
        <div class="pagination">
            {{ $messages->links('includes.pagination') }}
        </div>
    </div>
</x-app-layout>
