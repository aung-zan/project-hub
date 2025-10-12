<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index()
    {
        return response()->json([
            'success' => true,
            'message' => 'index',
        ]);
    }

    public function store()
    {
        return response()->json([
            'success' => true,
            'message' => 'store',
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

    public function destroy()
    {
        return response()->json([
            'success' => true,
            'message' => 'destroy',
        ]);
    }
}
