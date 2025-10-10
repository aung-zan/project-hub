<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function create()
    {
        return response()->json([
            'success' => true,
            'message' => 'create',
        ]);
    }

    public function update()
    {
        return response()->json([
            'success' => true,
            'message' => 'update',
        ]);
    }

    public function delete()
    {
        return response()->json([
            'success' => true,
            'message' => 'delete',
        ]);
    }
}
