@extends('layouts.app')

@section('content')
<div x-data="{ editPost: {{ session('editPost') ?? 'null' }} }" class="max-w-2xl mx-auto mt-10 px-4">
    <h1 class="text-2xl font-bold mb-6 text-center text-gray-800">Social Feed</h1>

    <!-- Form Buat Post Baru -->
    <div class="bg-white shadow rounded p-4 mb-6">
        <form action="{{ route('posts.store') }}" method="POST">
            @csrf
            <textarea name="content" rows="3" class="w-full border rounded p-2 text-sm focus:outline-none focus:ring focus:border-blue-300" placeholder="What's on your mind?">{{ old('content') }}</textarea>
            @error('content')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
            <div class="text-right mt-2">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm">Post</button>
            </div>
        </form>
    </div>

    <!-- Daftar Post -->
    @foreach ($posts as $post)
    <div class="bg-white shadow rounded p-4 mb-6">
        <div class="mb-2">
            <p class="text-sm text-gray-700">
                <strong>{{ $post->user->name }}</strong>
                <span class="text-xs text-gray-500 ml-2">{{ $post->created_at->diffForHumans() }}</span>
            </p>
            <p class="mt-1 text-gray-800">{{ $post->content }}</p>
        </div>

        <!-- Komentar -->
        <div class="border-t pt-2 mt-2 space-y-1">
            @foreach ($post->comments as $comment)
            <div class="ml-2 text-sm text-gray-600">
                <strong>{{ $comment->user->name }}</strong>: {{ $comment->content }}
            </div>
            @endforeach
        </div>

        <!-- Form Komentar -->
        <form action="{{ route('comments.store', $post) }}" method="POST" class="mt-3">
            @csrf
            <textarea name="content" rows="2" class="w-full border rounded p-2 text-sm focus:outline-none focus:ring focus:border-blue-300" placeholder="Tulis komentar...">{{ old('comment_content') }}</textarea>
            <div class="text-right mt-1">
                <button type="submit" class="px-3 py-1 bg-blue-500 text-white rounded text-sm hover:bg-blue-600">Kirim</button>
            </div>
        </form>

        <!-- Aksi Post -->
        <div class="flex items-center space-x-3 mt-4 text-sm">
            <!-- Like / Unlike -->
            <form action="{{ $post->likedByUsers->contains(auth()->user()) ? route('posts.unlike', $post) : route('posts.like', $post) }}" method="POST" class="inline">
                @csrf
                @if ($post->likedByUsers->contains(auth()->user()))
                @method('DELETE')
                <button type="submit" class="text-red-500 hover:underline">❤️ Unlike</button>
                @else
                <button type="submit" class="text-gray-500 hover:underline">🤍 Like</button>
                @endif
            </form>

            <span class="text-gray-500">{{ $post->likedByUsers->count() }} likes</span>

            <!-- tombol edit -->
            @if(auth()->id() === $post->user_id)
            <button @click="editPost = {{ $post->id }}" class="text-blue-500 hover:underline">Edit</button>

            <!-- Modal edit -->
            <div
                x-show="editPost === {{ $post->id }}"
                style="display: none"
                class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50"
                x-transition>
                <div @click.away="editPost = null" class="bg-white w-full max-w-lg p-6 rounded shadow-lg">
                    <h2 class="text-lg font-semibold mb-4">Edit Post</h2>
                    <form action="{{ route('posts.update', $post) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <textarea name="content" rows="4" class="w-full border rounded p-2 focus:outline-none focus:ring" required>{{ old('content', $post->content) }}</textarea>
                        @error('content')

                        @enderror

                        <div class="mt-4 flex justify-end space-x-2">
                            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Update</button>
                            <button type="button" @click="editPost = null" class="bg-gray-300 px-4 py-2 rounded hover:bg-gray-400">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>

            <form action="{{ route('posts.destroy', $post) }}" method="POST" class="inline ml-3" onsubmit="return confirm('Yakin mau hapus?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-red-500 hover:underline">Delete</button>
            </form>
            @endif
        </div>
    </div>
    @endforeach
</div>
@endsection