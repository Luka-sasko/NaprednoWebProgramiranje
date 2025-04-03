<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\User;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::where('user_id', auth()->id())->orWhereHas('teamMembers', function ($query) {
            $query->where('user_id', auth()->id());
        })->get();

        return view('projects.index', compact('projects'));
    }

    public function create()
    {
        //$this->authorize('create-project');
        $users = User::all();
        return view('projects.create', compact('users'));
    }

    public function store(Request $request)
    {
       // $this->authorize('create-project');

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'team_members' => 'nullable|array',
            'team_members.*' => 'exists:users,id',
        ]);

        $project = Project::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'user_id' => auth()->id(),
        ]);

        if ($request->has('team_members')) {
            $project->teamMembers()->sync($request->team_members);
        }

        return redirect()->route('projects.index')->with('success', 'Projekt kreiran!');
    }

    public function edit(Project $project)
    {
        //$this->authorize('update-project', $project);
        $users = User::all();
        return view('projects.edit', compact('project', 'users'));
    }

    public function update(Request $request, Project $project)
    {
        //$this->authorize('update-project', $project);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'team_members' => 'nullable|array',
            'team_members.*' => 'exists:users,id',
        ]);

        $project->update($request->only(['name', 'description', 'price', 'start_date', 'end_date']));

        if ($request->has('team_members')) {
            $project->teamMembers()->sync($request->team_members);
        } else {
            $project->teamMembers()->detach();
        }

        return redirect()->route('projects.index')->with('success', 'Projekt ažuriran!');
    }

    public function destroy($id)
    {
        $project = Project::findOrFail($id);



        $project->delete();

        return redirect()->route('projects.index')->with('success', 'Projekt je uspješno obrisan.');
    }
}