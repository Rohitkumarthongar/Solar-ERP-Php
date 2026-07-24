<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Team;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function index()
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $teams = Team::orderBy('name')->get();
        return view('admin.teams.index', compact('teams'));
    }

    public function create()
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $employees = \App\Models\Employee::where('is_active', true)->get();
        return view('admin.teams.create', compact('employees'));
    }

    public function store(Request $request)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:teams,name',
            'leader_id' => 'nullable|exists:employees,id',
            'description' => 'nullable|string',
            'installation_rate' => 'nullable|numeric|min:0',
            'site_visit_rate' => 'required|numeric|min:0',
            'service_rate' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive'
        ]);

        $validated['installation_rate'] = $validated['installation_rate'] ?? 0;

        Team::create($validated);
        return redirect()->route('admin.teams.index')->with('success', 'Team created successfully!');
    }

    public function edit($id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $team = Team::findOrFail($id);
        $employees = \App\Models\Employee::where('is_active', true)->get();
        return view('admin.teams.edit', compact('team', 'employees'));
    }

    public function update(Request $request, $id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $team = Team::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:teams,name,' . $id,
            'leader_id' => 'nullable|exists:employees,id',
            'description' => 'nullable|string',
            'installation_rate' => 'nullable|numeric|min:0',
            'site_visit_rate' => 'required|numeric|min:0',
            'service_rate' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive'
        ]);

        $validated['installation_rate'] = $validated['installation_rate'] ?? 0;

        $team->update($validated);
        return redirect()->route('admin.teams.index')->with('success', 'Team updated successfully!');
    }

    public function destroy($id)
    {
        if (!session('admin_logged_in')) return redirect()->route('admin.login');
        $team = Team::findOrFail($id);
        $team->delete();
        return redirect()->route('admin.teams.index')->with('success', 'Team removed successfully!');
    }
}
