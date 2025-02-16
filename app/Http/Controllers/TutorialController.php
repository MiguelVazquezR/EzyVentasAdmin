<?php

namespace App\Http\Controllers;

use App\Models\Tutorial;
use Illuminate\Http\Request;

class TutorialController extends Controller
{
    public function index()
    {
        $videos = Tutorial::all();

        return inertia('Tutorial/Index', compact('videos'));
    }

    public function create()
    {
        return inertia('Tutorial/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'youtube_id' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'status' => 'nullable|boolean',
        ]);

        Tutorial::create($request->all());

        return to_route('tutorials.index');
    }

    public function show(Tutorial $tutorial)
    {
        //
    }

    public function edit(Tutorial $tutorial)
    {
        return inertia('Tutorial/Edit', compact('tutorial'));
    }

    public function update(Request $request, Tutorial $tutorial)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'youtube_id' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'status' => 'nullable|boolean',
        ]);

        $tutorial->update($request->all());

        return to_route('tutorials.index');
    }

    public function destroy(Tutorial $tutorial)
    {
        //
    }
}
