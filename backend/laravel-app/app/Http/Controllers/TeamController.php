<?php

namespace App\Http\Controllers;

use App\Http\Requests\Team\TeamCreateRequest;
use App\Http\Requests\Team\TeamUpdateRequest;
use App\Models\Team;
use App\Services\TeamService;

class TeamController extends Controller
{
    public function __construct(private TeamService $teamService)
    {
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

    public function show(Team $team)
    {
        $team = $this->teamService->getTeam($team);

        return response()->json([
            'success' => true,
            'data' => $team,
        ]);
    }

    public function update(Team $team, TeamUpdateRequest $request)
    {
        $data = $request->validated();

        $team = $this->teamService->updateTeam($team, $data);

        return response()->json([
            'success' => true,
            'data' => $team,
        ]);
    }

    public function destroy(Team $team)
    {
        $team = $this->teamService->deleteTeam($team);

        return response()->json([
            'success' => true,
            'data' => $team,
        ]);
    }
}
