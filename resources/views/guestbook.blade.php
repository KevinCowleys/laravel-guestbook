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
                <div>
                    Comment:
                </div>
                <div>
                    <textarea name="message" id="createMessage" cols="30" rows="10"></textarea>
                </div>
                <input type="submit" value="Submit">
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
                function toggleEditAdmin(number) {
                    let reply = document.getElementById( number + '-editAdmin');
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
                <div class="flex-1 border rounded-lg px-4 py-2 sm:px-6 sm:py-4 leading-relaxed">
                    <div>{{ $message->user->name }} - {{ date("d M 'y", strtotime($message->updated_at)) }}</div>
                    {{ $message->comment }}
                    <div>
                        @auth
                            @if (Auth::user()->is_admin == true && !isset($message->reply))
                                <button onClick="toggleReply({{ $message->id }})">Reply</button>
                                <form action="/message/{{ $message->id }}/reply" id="{{ $message->id }}-reply" method="post" class="hidden">
                                    @csrf
                                    <textarea name="reply" cols="30" rows="10"></textarea>
                                    <input type="submit" value="Submit Reply">
                                </form>
                            @endif
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
                        @endauth
                    </div>
                    <div class="flex-1 bg-gray-100 rounded-lg px-4 py-2 sm:px-6 sm:py-4 leading-relaxed">
                        @if (isset($message->reply))
                            <div>Admin - {{ date("d M 'y", strtotime($message->updated_at)) }}</div>
                            {{ $message->reply }}
                            <div>
                                @auth
                                    @if (Auth::user()->is_admin == true)
                                        <button onClick="toggleEditAdmin({{ $message->id }})">Edit</button>
                                        <form action="/message/{{ $message->id }}/editAdmin" id="{{ $message->id }}-editAdmin" method="post" class="hidden">
                                            @csrf
                                            <textarea name="message" cols="30" rows="10">{{ $message->reply }}</textarea>
                                            <input type="submit" value="Edit">
                                        </form>
                                    @endif
                                @endauth
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</x-app-layout>
