<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store()
    {
        return response()->json([
            'success' => true,
            'message' => 'store',
        ]);
    }

    public function update()
    {
        return response()->json([
            'success' => true,
            'message' => 'update',
        ]);
    }

    public function destroy()
    {
        return response()->json([
            'success' => true,
            'message' => 'destroy',
        ]);
    }
}
