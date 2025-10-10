<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function create()
    {
        return response()->json([
            'success' => true,
            'message' => 'create',
        ]);
    }

    public function show()
    {
        return response()->json([
            'success' => true,
            'message' => 'show',
        ]);
    }

    public function update()
    {
        return response()->json([
            'success' => true,
            'message' => 'update',
        ]);
    }
}
