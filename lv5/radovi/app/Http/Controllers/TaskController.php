<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Application;
use App\Models\Task;
use App\Models\User;


class TaskController extends Controller
{

    public function index()
    {
        $tasks = Task::all();
        return view('tasks.index', compact('tasks'));
    }

    public function apply(Request $request, Task $task)
    {
        $request->validate([
            'priority' => 'required|integer|min:1|max:5',
        ]);

        $count = Application::where('student_id', auth()->id())->count();
        if ($count >= 5) {
            return back()->with('error', 'Možete prijaviti najviše 5 radova.');
        }

        Application::create([
            'student_id' => auth()->id(),
            'task_id' => $task->id,
            'priority' => $request->priority,
        ]);

        return back()->with('success', 'Uspješno prijavljeno.');
    }

    public function applications()
    {
        $tasks = Task::where('user_id', auth()->id())->with('applications.student')->get();
        return view('tasks.applications', compact('tasks'));
    }

    public function accept(Application $application)
    {
        if ($application->priority != 1) {
            return back()->with('error', 'Možete prihvatiti samo studenta s prioritetom 1.');
        }
        $application->accepted = true;
        $application->save();
        return back()->with('success', 'Student prihvaćen.');
    }

    public function create()
    {
        return view('tasks.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title_hr' => 'required|string|max:255',
            'title_en' => 'required|string|max:255',
            'description' => 'required|string',
            'study_type' => 'required|in:stručni,preddiplomski,diplomski',
        ]);

        Task::create([
            'user_id' => auth()->id(),
            'title_hr' => $request->title_hr,
            'title_en' => $request->title_en,
            'description' => $request->description,
            'study_type' => $request->study_type,
        ]);

        return redirect()->route('tasks.create')->with('success', 'Task created successfully.');
    }
}
