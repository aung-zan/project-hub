<?php

namespace App\Http\Controllers;

use App\Http\Requests\Team\TeamCreateRequest;
use App\Http\Requests\Team\TeamUpdateRequest;
use App\Services\TeamService;

class TeamController extends Controller
{
    private TeamService $teamService;

    public function __construct(TeamService $teamService)
    {
        $this->teamService = $teamService;
    }

    public function index()
    {
        $teams = $this->teamService->getAllTeam();

        return response()->json([
            'success' => true,
            'data' => $teams,
        ]);
    }

    public function store(TeamCreateRequest $request)
    {
        $data = $request->validated();
        $data['created_by'] = auth()->guard('api')->id();

        $team = $this->teamService->createTeam($data);

        return response()->json([
            'success' => true,
            'data' => $team,
        ]);
    }

    public function show(int $id)
    {
        $team = $this->teamService->getTeam($id);

        return response()->json([
            'success' => true,
            'data' => $team,
        ]);
    }

    public function update(int $id, TeamUpdateRequest $request)
    {
        $data = $request->validated();

        $team = $this->teamService->updateTeam($id, $data);

        return response()->json([
            'success' => true,
            'data' => $team,
        ]);
    }

    public function destroy(int $id)
    {
        $team = $this->teamService->deleteTeam($id);

        return response()->json([
            'success' => true,
            'data' => $team,
        ]);
    }
}
