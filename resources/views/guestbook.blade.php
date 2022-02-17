<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Guestbook') }}
        </h2>
    </x-slot>

    <div class="py-12">
        @foreach ($messages as $messages)
            @if (isset($messages->reply))
                
            @endif
        @endforeach
    </div>
</x-app-layout>
