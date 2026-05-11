<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApiProject;
use App\Models\ApiRequestHistory;
use App\Models\ApiRoute;
use App\Models\PersonalProject;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.dashboard', [
            'apiCount' => ApiProject::query()->count(),
            'routeCount' => ApiRoute::query()->count(),
            'historyCount' => ApiRequestHistory::query()->count(),
            'personalProjectCount' => PersonalProject::query()->count(),
            'publishedPersonalProjectCount' => PersonalProject::query()->where('is_published', true)->count(),
            'recentHistories' => ApiRequestHistory::query()
                ->with(['project', 'route'])
                ->latest()
                ->limit(8)
                ->get(),
        ]);
    }
}
