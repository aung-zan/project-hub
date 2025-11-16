<?php

namespace Tests\Feature\Project;

use App\Models\Project;
use App\Models\ProjectUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Feature\TestHelper;
use Tests\FeatureTestCase;

class ProjectIndexTest extends FeatureTestCase
{
    use RefreshDatabase;
    use TestHelper;

    protected string $method = 'get';
    protected string $url = 'http://localhost/api/projects';
    private array $projectData = [
        'name' => 'another testing',
        'status' => 'active',
    ];

    /**
     * Test for user cannot see membered projects.
     */
    public function testUserCannotSeeMemberedProjects(): void
    {
        list($token, $id) = $this->login();

        $projectData = $this->projectData;
        $projectData['created_by'] = $id;

        Project::factory()->count(2)->create($projectData);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->getJson($this->url);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => []
            ]);
    }

    /**
     * Test for user can see only membered projects.
     */
    public function testUserCanSeeOnlyMemberedProjects(): void
    {
        list($token, $id) = $this->login();

        $projectData = $this->projectData;
        $projectData['created_by'] = $id;

        $projects = Project::factory()->count(2)->create($projectData);
        $firstProject = $projects->first();

        ProjectUser::factory()->create([
            'project_id' => $firstProject->id,
            'user_id' => $id,
            'role' => 'owner',
            'created_by' => $id,
        ]);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->getJson($this->url);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    [
                        'id' => $firstProject->id,
                        'name' => $projectData['name'],
                        'status' => $projectData['status'],
                    ]
                ]
            ]);
    }
}
