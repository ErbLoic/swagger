<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApiProject;
use App\Models\ApiRequestHistory;
use App\Models\ApiRoute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Throwable;

class PostmanController extends Controller
{
    public function index(Request $request): View
    {
        $selectedRoute = null;
        $selectedProject = null;

        if ($request->filled('route')) {
            $selectedRoute = ApiRoute::query()->with('project')->find($request->integer('route'));
            $selectedProject = $selectedRoute?->project;
        }

        return view('admin.postman.index', [
            'projects' => ApiProject::query()
                ->where('is_active', true)
                ->with(['routes' => fn ($query) => $query->orderBy('uri')->orderBy('method')])
                ->orderBy('name')
                ->get(),
            'selectedRoute' => $selectedRoute,
            'selectedProject' => $selectedProject,
            'histories' => ApiRequestHistory::query()
                ->with(['project', 'route'])
                ->latest()
                ->limit(15)
                ->get(),
            'result' => null,
        ]);
    }

    public function send(Request $request): View
    {
        $data = $request->validate([
            'api_project_id' => ['required', 'exists:api_projects,id'],
            'api_route_id' => ['nullable', 'exists:api_routes,id'],
            'method' => ['required', 'string', 'max:16'],
            'path' => ['required', 'string'],
            'headers' => ['nullable', 'string'],
            'query_params' => ['nullable', 'string'],
            'body' => ['nullable', 'string'],
            'body_type' => ['required', 'in:json,form,raw'],
        ]);

        $project = ApiProject::query()->findOrFail($data['api_project_id']);
        $route = isset($data['api_route_id']) ? ApiRoute::query()->find($data['api_route_id']) : null;
        $method = strtoupper($data['method']);
        $url = $this->buildUrl($project->base_url, $data['path']);
        $headers = $this->parseKeyValue($data['headers'] ?? '');
        $query = $this->parseKeyValue($data['query_params'] ?? '');
        $started = microtime(true);

        try {
            $pending = Http::withHeaders($headers)->timeout(30);

            if ($data['body_type'] === 'json') {
                $body = $this->jsonBody($data['body'] ?? '');
                $response = $pending->send($method, $url, [
                    'query' => $query,
                    'json' => $body,
                ]);
            } elseif ($data['body_type'] === 'form') {
                $body = $this->parseKeyValue($data['body'] ?? '');
                $response = $pending->asForm()->send($method, $url, [
                    'query' => $query,
                    'form_params' => $body,
                ]);
            } else {
                $body = $data['body'] ?? '';
                $response = $pending->withBody($body)->send($method, $url, [
                    'query' => $query,
                ]);
            }

            $duration = (int) round((microtime(true) - $started) * 1000);
            $result = [
                'status' => $response->status(),
                'duration' => $duration,
                'headers' => $response->headers(),
                'body' => $this->prettyBody($response->body()),
                'error' => null,
            ];
        } catch (Throwable $exception) {
            $duration = (int) round((microtime(true) - $started) * 1000);
            $result = [
                'status' => null,
                'duration' => $duration,
                'headers' => [],
                'body' => null,
                'error' => $exception->getMessage(),
            ];
        }

        ApiRequestHistory::query()->create([
            'user_id' => $request->user()?->id,
            'api_project_id' => $project->id,
            'api_route_id' => $route?->id,
            'method' => $method,
            'url' => $url,
            'request_headers' => $headers,
            'query_params' => $query,
            'request_body' => $data['body'] ?? null,
            'status_code' => $result['status'],
            'duration_ms' => $result['duration'],
            'response_headers' => $result['headers'],
            'response_body' => $result['body'],
            'error' => $result['error'],
        ]);

        return view('admin.postman.index', [
            'projects' => ApiProject::query()
                ->where('is_active', true)
                ->with(['routes' => fn ($query) => $query->orderBy('uri')->orderBy('method')])
                ->orderBy('name')
                ->get(),
            'selectedRoute' => $route,
            'selectedProject' => $project,
            'histories' => ApiRequestHistory::query()
                ->with(['project', 'route'])
                ->latest()
                ->limit(15)
                ->get(),
            'result' => $result,
            'oldInput' => $data,
        ]);
    }

    private function buildUrl(string $baseUrl, string $path): string
    {
        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        return rtrim($baseUrl, '/').'/'.ltrim($path, '/');
    }

    private function parseKeyValue(string $value): array
    {
        $value = trim($value);

        if ($value === '') {
            return [];
        }

        $decoded = json_decode($value, true);

        if (is_array($decoded)) {
            return $decoded;
        }

        $pairs = [];

        foreach (preg_split('/\r\n|\r|\n/', $value) as $line) {
            $line = trim($line);

            if ($line === '') {
                continue;
            }

            [$key, $itemValue] = array_pad(explode(':', $line, 2), 2, '');
            $pairs[trim($key)] = trim($itemValue);
        }

        return array_filter($pairs, fn ($key) => $key !== '', ARRAY_FILTER_USE_KEY);
    }

    private function jsonBody(string $value): array
    {
        $value = trim($value);

        if ($value === '') {
            return [];
        }

        $decoded = json_decode($value, true);

        return is_array($decoded) ? $decoded : ['raw' => $value];
    }

    private function prettyBody(string $body): string
    {
        $decoded = json_decode($body, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return $body;
        }

        return json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }
}
