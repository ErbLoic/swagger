<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApiProject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class ManifestImportController extends Controller
{
    public function __invoke(ApiProject $api): RedirectResponse
    {
        $response = Http::acceptJson()->timeout(15)->get($api->manifest_url);

        if (! $response->successful()) {
            throw ValidationException::withMessages([
                'manifest' => 'Manifest indisponible: HTTP '.$response->status().'.',
            ]);
        }

        $payload = $response->json();

        if (! is_array($payload) || ! isset($payload['routes']) || ! is_array($payload['routes'])) {
            throw ValidationException::withMessages([
                'manifest' => 'Le manifest doit contenir un tableau "routes".',
            ]);
        }

        $imported = 0;

        foreach ($payload['routes'] as $route) {
            if (! is_array($route) || empty($route['method']) || empty($route['uri'])) {
                continue;
            }

            $api->routes()->updateOrCreate(
                [
                    'method' => strtoupper((string) $route['method']),
                    'uri' => $this->normalizeUri((string) $route['uri']),
                ],
                [
                    'name' => $route['name'] ?? null,
                    'action' => $route['action'] ?? null,
                    'middleware' => $this->arrayValue($route['middleware'] ?? []),
                    'parameters' => $this->arrayValue($route['parameters'] ?? []),
                    'headers' => $this->arrayValue($route['headers'] ?? []),
                    'body_schema' => $this->arrayValue($route['body_schema'] ?? $route['body'] ?? []),
                    'metadata' => $route,
                ],
            );

            $imported++;
        }

        return redirect()
            ->route('admin.apis.show', $api)
            ->with('status', $imported.' route(s) synchronisée(s).');
    }

    private function normalizeUri(string $uri): string
    {
        return '/'.ltrim($uri, '/');
    }

    private function arrayValue(mixed $value): array
    {
        if (is_array($value)) {
            return $value;
        }

        if ($value === null || $value === '') {
            return [];
        }

        return [(string) $value];
    }
}
