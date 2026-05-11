<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApiProject;
use App\Models\ApiRoute;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ApiRouteController extends Controller
{
    public function create(ApiProject $api): View
    {
        return view('admin.routes.form', [
            'project' => $api,
            'route' => new ApiRoute(['method' => 'GET', 'uri' => '/api']),
        ]);
    }

    public function store(Request $request, ApiProject $api): RedirectResponse
    {
        $api->routes()->create($this->validated($request));

        return redirect()
            ->route('admin.apis.show', $api)
            ->with('status', 'Route ajoutée.');
    }

    public function edit(ApiProject $api, ApiRoute $route): View
    {
        abort_unless($route->api_project_id === $api->id, 404);

        return view('admin.routes.form', [
            'project' => $api,
            'route' => $route,
        ]);
    }

    public function update(Request $request, ApiProject $api, ApiRoute $route): RedirectResponse
    {
        abort_unless($route->api_project_id === $api->id, 404);

        $route->update($this->validated($request));

        return redirect()
            ->route('admin.apis.show', $api)
            ->with('status', 'Route mise à jour.');
    }

    public function destroy(ApiProject $api, ApiRoute $route): RedirectResponse
    {
        abort_unless($route->api_project_id === $api->id, 404);

        $route->delete();

        return redirect()
            ->route('admin.apis.show', $api)
            ->with('status', 'Route supprimée.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'method' => ['required', 'string', 'max:16'],
            'uri' => ['required', 'string', 'max:255'],
            'name' => ['nullable', 'string', 'max:255'],
            'action' => ['nullable', 'string', 'max:255'],
            'middleware' => ['nullable', 'string'],
            'parameters' => ['nullable', 'string'],
            'headers' => ['nullable', 'string'],
            'body_schema' => ['nullable', 'string'],
        ]);

        $data['method'] = strtoupper($data['method']);
        $data['middleware'] = $this->lines($data['middleware'] ?? null);
        $data['parameters'] = $this->jsonOrLines($data['parameters'] ?? null);
        $data['headers'] = $this->jsonOrLines($data['headers'] ?? null);
        $data['body_schema'] = $this->jsonOrLines($data['body_schema'] ?? null);

        return $data;
    }

    private function lines(?string $value): array
    {
        return collect(preg_split('/\r\n|\r|\n/', (string) $value))
            ->map(fn ($line) => trim($line))
            ->filter()
            ->values()
            ->all();
    }

    private function jsonOrLines(?string $value): array
    {
        $value = trim((string) $value);

        if ($value === '') {
            return [];
        }

        $decoded = json_decode($value, true);

        return is_array($decoded) ? $decoded : $this->lines($value);
    }
}
