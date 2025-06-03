<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Redirect;

class PostController extends Controller
{
    use AuthorizesRequests;
    public function index()
    {
        $posts = Post::with(['user', 'likedByUsers', 'comments.user'])->latest()->get();
        return view('posts.index', compact('posts'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
        ]);
        // Simpan post ke db, user_id dari user yg login
        Post::create([
            'user_id' => Auth::id(),
            'content' => $request->content,
        ]);


        return redirect()->back()->with('success', 'Post created!');
    }
    public function edit(Post $post)
    {
        // Pastikan user hanya bisa edit post miliknya
        $this->authorize('update', $post);

        return view('posts.edit', compact('post'));
    }

    public function update(Request $request, Post $post)
    {
        $this->authorize('update', $post);

        try {
            $request->validate([
                'content' => 'required|string|max:1000',
            ]);
        } catch (ValidationException $e) {
            return Redirect::back()
                ->withErrors($e->validator)
                ->withInput()
                ->with('editPost', $post->id);
        }

        $post->update([
            'content' => $request->content,
        ]);

        return redirect('/')->with('success', 'Post berhasil diupdate');
    }


    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);

        $post->delete();

        return redirect('/')->with('success', 'Post berhasil dihapus');
    }
    public function like(Post $post)
    {
        $user = Auth::user();

        if (!$post->likedByUsers->contains($user)) {
            $post->likedByUsers()->attach($user->id);
        }

        return redirect()->back();
    }

    public function unlike(Post $post)
    {
        $user = Auth::user();

        if ($post->likedByUsers->contains($user)) {
            $post->likedByUsers()->detach($user->id);
        }

        return redirect()->back();
    }
}
