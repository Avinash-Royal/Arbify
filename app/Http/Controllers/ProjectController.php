<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddLanguageToProject;
use App\Http\Requests\StoreProject;
use App\Language;
use App\Project;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class ProjectController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(): View
    {
        $projects = Project::paginate(15);

        return view('projects.index', [
            'projects' => $projects,
        ]);
    }

    public function create(): View
    {
        $languages = Language::all();

        return view('projects.form', [
            'languages' => $languages,
        ]);
    }

    public function store(StoreProject $request): Response
    {
        $project = new Project();
        $project->name = $request->name;
        $project->save();

        return redirect()->route('projects.index')
            ->with('success', "Added <b>$project->name</b> successfully.");
    }

    public function edit(Project $project): View
    {
        return view('projects.form', [
            'project' => $project,
        ]);
    }

    public function update(StoreProject $request, Project $project): Response
    {
        $project->name = $request->name;
        $project->save();

        return redirect()->route('projects.index')
            ->with('success', "Updated <b>$project->name</b> successfully.");
    }

    public function destroy(Project $project): Response
    {
        $project->delete();

        return redirect()->route('projects.index')
            ->with('success', "Deleted <b>$project->name</b> successfully.");
    }

    public function addLanguage(Project $project): View
    {
        $languages = Language::allExceptAlreadyInProject($project);

        return view('projects.add-language', [
            'project' => $project,
            'languages' => $languages,
        ]);
    }

    public function postAddLanguage(AddLanguageToProject $request, Project $project): Response
    {
        $language = Language::find($request->language);

        $project->languages()->syncWithoutDetaching($language);

        return redirect()->route('messages.index', $project)
            ->with('success', "Added <b>$language->code</b> to <b>$project->name</b> successfully.");
    }
}