<?php

namespace App\Http\Controllers;

use App\Http\Requests\TeamUser\TeamUserCreateRequest;
use App\Models\Team;
use App\Services\TeamUserService;

class TeamUserController extends Controller
{
    public function __construct(private TeamUserService $teamUserService)
    {
    }

    public function store(Team $team, TeamUserCreateRequest $request)
    {
        $data = $request->validated();
        $data['auth_id'] = auth()->guard('api')->id();

        $memberIds = $this->teamUserService->createTeamUser($team, $data);

        return response()->json([
            'success' => true,
            'data' => [
                'members' => $memberIds
            ],
        ], 200);
    }

    public function destroy(Team $team, int $memberId)
    {
        $this->teamUserService->removeTeamUser($team, $memberId);

        return response()->json([
            'success' => true,
            'message' => 'Successfully remove a member.'
        ], 200);
    }
}
