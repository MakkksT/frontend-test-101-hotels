<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;

class CommentController extends Controller
{
    public function index()
    {
        $comments = Comment::all()->toArray();

        $comments = array_map(
            function ($item) {
                return [
                    'id' => $item['id'],
                    'name' => $item['name'],
                    'text' => $item['text'],
                    'date' => $item['date'],
                    'rating' => $item['rating'],
                ];
            },
            $comments
        );

        return array_reverse($comments);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'text' => 'required|string',
            'date' => 'required|string',
            'rating' => 'required|integer|min:1|max:10',
        ]);

        $comment = new Comment([
            'name' => $request->input('name'),
            'text' => $request->input('text'),
            'date' => $request->input('date'),
            'rating' => $request->input('rating'),
        ]);

        $comment->save();

        return response()->json([
            'id' => $comment->id,
            'name' => $comment->name,
            'text' => $comment->text,
            'date' => $comment->date,
            'rating' => $comment->rating,
        ]);
    }

    public function show($id)
    {
        $comment = Comment::find($id);

        return response()->json($comment);
    }

    public function update($id, Request $request)
    {
        $comment = Comment::find($id);

        $comment->update($request->all());

        return response()->json('Comment updated!');
    }

    public function destroy($id)
    {
        $comment = Comment::find($id);

        $comment->delete();

        return response()->json('Comment deleted!');
    }
}