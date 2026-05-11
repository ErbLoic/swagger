<?php

namespace Tests\Feature;

use App\Models\ApiProject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ApiProjectManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_and_import_manifest(): void
    {
        $user = User::factory()->create();

        Http::fake([
            'https://example.test/manifest' => Http::response([
                'name' => 'Example',
                'routes' => [
                    [
                        'method' => 'GET',
                        'uri' => '/api/users',
                        'name' => 'users.index',
                        'action' => 'UserController@index',
                        'middleware' => ['api'],
                        'parameters' => [],
                    ],
                ],
            ]),
        ]);

        $this->actingAs($user)
            ->post(route('admin.apis.store'), [
                'name' => 'Example API',
                'base_url' => 'https://example.test',
                'manifest_url' => 'https://example.test/manifest',
                'description' => 'Demo',
                'is_active' => '1',
            ])
            ->assertRedirect();

        $project = ApiProject::query()->firstOrFail();

        $this->actingAs($user)
            ->post(route('admin.apis.import', $project))
            ->assertRedirect(route('admin.apis.show', $project));

        $this->assertDatabaseHas('api_routes', [
            'api_project_id' => $project->id,
            'method' => 'GET',
            'uri' => '/api/users',
            'name' => 'users.index',
        ]);
    }

    public function test_import_reports_invalid_manifest(): void
    {
        $user = User::factory()->create();
        $project = ApiProject::query()->create([
            'name' => 'Bad API',
            'base_url' => 'https://bad.test',
            'manifest_url' => 'https://bad.test/manifest',
            'is_active' => true,
        ]);

        Http::fake([
            'https://bad.test/manifest' => Http::response(['ok' => true]),
        ]);

        $this->actingAs($user)
            ->from(route('admin.apis.show', $project))
            ->post(route('admin.apis.import', $project))
            ->assertRedirect(route('admin.apis.show', $project))
            ->assertSessionHasErrors('manifest');
    }
}
