<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PersonalProject;
use Illuminate\Http\JsonResponse;

class PersonalProjectApiController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => PersonalProject::query()
                ->where('is_published', true)
                ->orderBy('sort_order')
                ->latest()
                ->get(),
        ]);
    }

    public function show(PersonalProject $project): JsonResponse
    {
        abort_unless($project->is_published, 404);

        return response()->json([
            'data' => $project,
        ]);
    }
}
