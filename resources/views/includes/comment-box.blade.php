<form action="{{ $url }}" method="POST" id="{{ $id }}" class="{{ $hide === true ? 'hidden' : '' }}">
    @csrf
    <div>
        <textarea rows="4" cols="50" name="{{ $name }}" value="testing"
            class="bg-white rounded border border-gray-200 py-1 px-3 block focus:ring-blue-500 focus:border-blue-500 text-gray-700 shadow dark:bg-zinc-800 dark:border-zinc-900 dark:text-gray-400"
            placeholder="Enter your comment">{{ $comment ?? '' }}</textarea>
        <input type="submit" value="Submit"
            class="my-2 inline-block px-6 py-2.5 bg-blue-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-blue-700 hover:shadow-lg focus:bg-blue-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-blue-800 active:shadow-lg transition duration-150 ease-in-out cursor-pointer">
    </div>
</form>
