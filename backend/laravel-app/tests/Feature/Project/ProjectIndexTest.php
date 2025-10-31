<?php

namespace Tests\Feature\Project;

use App\Models\Project;
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
     * Test for user access the data with right token.
     */
    public function testUserCanAccessIndexWithRightToken(): void
    {
        list($token, $id) = $this->login();

        $projectData = $this->projectData;
        $projectData['created_by'] = $id;

        Project::factory()->create($projectData);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->getJson($this->url);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    [
                        'name' => $projectData['name'],
                        'status' => $projectData['status'],
                    ]
                ]
            ]);
    }
}
