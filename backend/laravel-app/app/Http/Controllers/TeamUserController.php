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
        ]);
    }
}
