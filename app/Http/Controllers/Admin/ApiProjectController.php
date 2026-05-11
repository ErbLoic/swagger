<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApiProject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ApiProjectController extends Controller
{
    public function index(): View
    {
        return view('admin.apis.index', [
            'projects' => ApiProject::query()
                ->withCount('routes')
                ->latest()
                ->paginate(12),
        ]);
    }

    public function create(): View
    {
        return view('admin.apis.form', [
            'project' => new ApiProject(['is_active' => true]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $project = ApiProject::query()->create($this->validated($request));

        return redirect()
            ->route('admin.apis.show', $project)
            ->with('status', 'API créée.');
    }

    public function show(ApiProject $api): View
    {
        return view('admin.apis.show', [
            'project' => $api->load(['routes' => fn ($query) => $query->orderBy('uri')->orderBy('method')]),
        ]);
    }

    public function edit(ApiProject $api): View
    {
        return view('admin.apis.form', [
            'project' => $api,
        ]);
    }

    public function update(Request $request, ApiProject $api): RedirectResponse
    {
        $api->update($this->validated($request));

        return redirect()
            ->route('admin.apis.show', $api)
            ->with('status', 'API mise à jour.');
    }

    public function destroy(ApiProject $api): RedirectResponse
    {
        $api->delete();

        return redirect()
            ->route('admin.apis.index')
            ->with('status', 'API supprimée.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'base_url' => ['required', 'url', 'max:255'],
            'manifest_url' => ['required', 'url', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]) + ['is_active' => false];
    }
}
