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

    public function delete()
    {
        return response()->json([
            'success' => true,
            'message' => 'delete',
        ]);
    }
}
