<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request, $id)
    {
        $request->validate([
            'comment' => 'required|string'
        ]);

        $comment = Comment::create([
            'identifier' => $request->identifier,
            'key_id' => $id,
            'user_id' => auth()->id(),
            'comment' => $request->comment
        ]);

        return response()->json([
            'status' => 'success',
            'data' => [
                'name' => $comment->user->u_name,
                'datetime' => $comment->created_at->format('d/m/Y H:i'),
                'comment' => $comment->comment
            ]
        ]);
    }
}
