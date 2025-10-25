<?php

namespace App\Http\Controllers;

use App\Http\Requests\TeamUser\TeamUserCreateRequest;
use App\Services\TeamUserService;

class TeamUserController extends Controller
{
    private TeamUserService $teamUserService;

    public function __construct(TeamUserService $teamUserService)
    {
        $this->teamUserService = $teamUserService;
    }

    public function store(int $id, TeamUserCreateRequest $request)
    {
        $data = $request->toArray();
        $data['auth_id'] = auth()->guard('api')->id();

        $memberIds = $this->teamUserService->createTeamUser($id, $data);

        return response()->json([
            'success' => true,
            'data' => [
                'members' => $memberIds
            ],
        ], 200);
    }

    public function destroy(int $teamId, int $memberId)
    {
        $this->teamUserService->removeTeamUser($teamId, $memberId);

        return response()->json([
            'success' => true,
            'message' => 'Successfully remove a member.'
        ], 200);
    }
}
