<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Guestbook') }}
        </h2>
    </x-slot>

    <div class="py-12">
        @auth
            <form action="/message" method="post">
                @csrf
                <textarea name="message" id="createMessage" cols="30" rows="10"></textarea>
                <input type="submit" value="submit">
            </form>
            <script>
                function toggleEdit(number) {
                    let edit = document.getElementById( number + '-edit');
                    if (edit.classList.contains('hidden')) {
                        edit.classList = ""
                    } else {
                        edit.classList = "hidden"
                    }
                }
            </script>
            @if (Auth::user()->is_admin == true)
            <script>
                function toggleReply(number) {
                    let reply = document.getElementById( number + '-reply');
                    if (reply.classList.contains('hidden')) {
                        reply.classList = ""
                    } else {
                        reply.classList = "hidden"
                    }
                }
            </script>
            @endif
        @endauth
        @foreach ($messages as $message)
            <div>
                <div>
                    {{ $message->comment }}
                    @auth
                        @if (Auth::user()->id == $message->user_id || Auth::user()->is_admin)
                            <button onClick="toggleEdit({{ $message->id }})">Edit</button>
                            <form action="/message/{{ $message->id }}/edit" id="{{ $message->id }}-edit" method="post" class="hidden">
                                @csrf
                                <textarea name="message" cols="30" rows="10">{{ $message->comment }}</textarea>
                                <input type="submit" value="Edit">
                            </form>
                            <form action="/message/{{ $message->id }}/delete" method="post">
                                @csrf
                                <input type="submit" value="Delete">
                            </form>
                        @endif
                        @if (Auth::user()->is_admin == true)
                            <button onClick="toggleReply({{ $message->id }})">Reply</button>
                            <form action="/message/{{ $message->id }}/reply" id="{{ $message->id }}-reply" method="post" class="hidden">
                                @csrf
                                <textarea name="reply" cols="30" rows="10"></textarea>
                                <input type="submit" value="Submit Reply">
                            </form>
                        @endif
                    @endauth
                </div>
                <div>
                    @if (isset($message->reply))
                        {{ $message->reply }}
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</x-app-layout>
