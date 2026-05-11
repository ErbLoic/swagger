<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Route;

class ManifestController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $routes = collect(Route::getRoutes())
            ->flatMap(function ($route) {
                return collect($route->methods())
                    ->reject(fn (string $method) => $method === 'HEAD')
                    ->map(fn (string $method) => [
                        'method' => $method,
                        'uri' => '/'.ltrim($route->uri(), '/'),
                        'name' => $route->getName(),
                        'action' => $route->getActionName(),
                        'middleware' => array_values($route->middleware()),
                        'parameters' => $route->parameterNames(),
                    ]);
            })
            ->values();

        return response()->json([
            'name' => config('app.name', 'API Exemple'),
            'routes' => $routes,
        ]);
    }
}
