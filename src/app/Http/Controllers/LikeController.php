<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Like;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    /**
     * @param  \Illuminate\Http\Request $request
     * @param  string $item_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, string $item_id)
    {
        if (!Auth::check()) {
            return response()->json(['status' => 'error', 'message' => 'unauthenticated'], 401);
        }

        $user_id = Auth::id();

        $like = Like::where('user_id', $user_id)
            ->where('item_id', $item_id)
            ->first();

        if ($like) {
            $like->delete();
            $action = 'detached';
        } else {
            Like::create([
                'user_id' => $user_id,
                'item_id' => $item_id,
            ]);
            $action = 'attached';
        }

        $like_count = Like::where('item_id', $item_id)->count();

        return response()->json([
            'status' => 'success',
            'action' => $action,
            'like_count' => $like_count,
        ]);
    }
}
