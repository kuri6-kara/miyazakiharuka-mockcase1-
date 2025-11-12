<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\CommentRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\Comment;

class CommentController extends Controller
{
    /**

     * @param
     * @return
     */

    public function __construct()
    {
        $this->middleware('auth');
    }


    public function store(CommentRequest $request)
    {
        Comment::create([
            'user_id' => Auth::id(),
            'item_id' => $request->item_id,
            'comment' => $request->comment,
        ]);

        return redirect()->route('item.show', ['item_id' => $request->item_id])
            ->with('success', 'コメントを送信しました。');
    }
}
