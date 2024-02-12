<?php

namespace App\Http\Controllers\Api;

use App\Models\Post;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Resources\PostResource;

use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    /**
     * index
     * 
     * @return void
     */
    public function index()
    {
        $posts = Post::latest()->paginate(5);
        return new PostResource(true, 'List Data Posts', $posts);
    }
    /**
     * store
     * 
     * @param mixed $request
     * @return void
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'title' => 'required',
            'content' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $image = $request->file('image');
        $image->storeAs('public/posts', $image->hashName());

        $post = Post::create([
            'image' => $image->hashName(),
            'title' => $request->title,
            'content' => $request->content,
        ]);

        return new PostResource(true, 'Data Post Berhasil Ditambahkan', $post);
    }
    /**
     * get
     * 
     * @param mixed $request
     * @return void
     */
    public function show($id)
    {
        $posts = Post::find($id);

        return new PostResource(true, 'Detail Data Posts', $posts);
    }

    public function destroy($id)
    {
        $post = Post::find($id);
        if (!$post) {
            return response()->json(['message' => 'Post not found'], 404);
        }
        $post->delete();
        return new PostResource(true, 'Data Post Berhasil di hapus!', $post);
    }

    public function update(Request $request, Post $post)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'title' => 'required',
            'content' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if ($request->file('image')) {
            $image = $request->file('image');
            $image->storeAs('public/posts', $image->hashName());

            $post->update([
                'image' => $image->hashName(),
                'title' => $request->title,
                'content' => $request->content,
            ]);
        } else {
            $post->update([
                'title' => $request->title,
                'content' => $request->content,
            ]);
        }

        return new PostResource(true, 'Data Post Berhasil di ubah!', $post);
    }
}
