<?php

namespace Tests\Feature;

use App\Models\ApiProject;
use App\Models\ApiRoute;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PostmanTest extends TestCase
{
    use RefreshDatabase;

    public function test_postman_sends_request_and_stores_history(): void
    {
        $user = User::factory()->create();
        $project = ApiProject::query()->create([
            'name' => 'Example API',
            'base_url' => 'https://example.test',
            'manifest_url' => 'https://example.test/manifest',
            'is_active' => true,
        ]);
        $route = ApiRoute::query()->create([
            'api_project_id' => $project->id,
            'method' => 'POST',
            'uri' => '/api/users',
        ]);

        Http::fake([
            'https://example.test/api/users?active=1' => Http::response(['created' => true], 201, ['X-Test' => 'ok']),
        ]);

        $this->actingAs($user)
            ->post(route('admin.postman.send'), [
                'api_project_id' => $project->id,
                'api_route_id' => $route->id,
                'method' => 'POST',
                'path' => '/api/users',
                'headers' => 'Accept: application/json',
                'query_params' => 'active: 1',
                'body_type' => 'json',
                'body' => '{"name":"Ada"}',
            ])
            ->assertOk()
            ->assertSee('Status 201');

        $this->assertDatabaseHas('api_request_histories', [
            'user_id' => $user->id,
            'api_project_id' => $project->id,
            'api_route_id' => $route->id,
            'method' => 'POST',
            'url' => 'https://example.test/api/users',
            'status_code' => 201,
        ]);
    }
}
