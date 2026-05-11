<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PersonalProject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PersonalProjectController extends Controller
{
    public function index(): View
    {
        return view('admin.personal-projects.index', [
            'projects' => PersonalProject::query()
                ->orderBy('sort_order')
                ->latest()
                ->paginate(12),
        ]);
    }

    public function create(): View
    {
        return view('admin.personal-projects.form', [
            'project' => new PersonalProject([
                'is_published' => true,
                'sort_order' => 0,
            ]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('personal-projects', 'public');
        }

        $project = PersonalProject::query()->create($data);

        return redirect()
            ->route('admin.personal-projects.show', $project)
            ->with('status', 'Projet perso créé.');
    }

    public function show(PersonalProject $personalProject): View
    {
        return view('admin.personal-projects.show', [
            'project' => $personalProject,
        ]);
    }

    public function edit(PersonalProject $personalProject): View
    {
        return view('admin.personal-projects.form', [
            'project' => $personalProject,
        ]);
    }

    public function update(Request $request, PersonalProject $personalProject): RedirectResponse
    {
        $data = $this->validated($request, $personalProject);

        if ($request->hasFile('image')) {
            if ($personalProject->image_path) {
                Storage::disk('public')->delete($personalProject->image_path);
            }

            $data['image_path'] = $request->file('image')->store('personal-projects', 'public');
        }

        if ($request->boolean('remove_image') && $personalProject->image_path) {
            Storage::disk('public')->delete($personalProject->image_path);
            $data['image_path'] = null;
        }

        $personalProject->update($data);

        return redirect()
            ->route('admin.personal-projects.show', $personalProject)
            ->with('status', 'Projet perso mis à jour.');
    }

    public function destroy(PersonalProject $personalProject): RedirectResponse
    {
        if ($personalProject->image_path) {
            Storage::disk('public')->delete($personalProject->image_path);
        }

        $personalProject->delete();

        return redirect()
            ->route('admin.personal-projects.index')
            ->with('status', 'Projet perso supprimé.');
    }

    private function validated(Request $request, ?PersonalProject $project = null): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:personal_projects,slug,'.($project?->id ?? 'NULL')],
            'summary' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'max:4096'],
            'project_url' => ['nullable', 'url', 'max:255'],
            'github_url' => ['nullable', 'url', 'max:255'],
            'technologies' => ['nullable', 'string'],
            'is_featured' => ['nullable', 'boolean'],
            'is_published' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $data['slug'] = $data['slug'] ?: Str::slug($data['title']);
        $data['technologies'] = $this->technologies($data['technologies'] ?? '');
        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_published'] = $request->boolean('is_published');
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);

        unset($data['image']);

        return $data;
    }

    private function technologies(string $value): array
    {
        return collect(preg_split('/,|\r\n|\r|\n/', $value))
            ->map(fn ($technology) => trim($technology))
            ->filter()
            ->values()
            ->all();
    }
}
