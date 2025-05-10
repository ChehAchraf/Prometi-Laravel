<?php

namespace App\Http\Controllers;

use App\Models\TimeEntry;
use App\Models\Project;
use App\Models\Overtime;
use Illuminate\Http\Request;

class TimeEntryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $timeEntries = TimeEntry::with(['user', 'project'])->latest()->paginate(10);
        return view('time-entries.index', compact('timeEntries'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $projects = Project::where('status', 'active')->get();
        return view('time-entries.create', compact('projects'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'project_id' => 'required|exists:projects,id',
            'date' => 'required|date',
            'check_in' => 'required|date_format:H:i',
            'check_out' => 'required|date_format:H:i|after:check_in',
            'notes' => 'nullable|string',
            'status' => 'required|in:present,absent,late',
            'overtime_hours' => 'nullable|numeric|min:0'
        ]);

        $timeEntry = TimeEntry::create($validated);

        // Create overtime request if there are overtime hours
        if ($validated['overtime_hours'] > 0) {
            $overtime = Overtime::create([
                'time_entry_id' => $timeEntry->id,
                'user_id' => $validated['user_id'],
                'project_id' => $validated['project_id'],
                'date' => $validated['date'],
                'hours' => $validated['overtime_hours'],
                'status' => 'pending'
            ]);
        }

        return redirect()->route('time-entries.show', $timeEntry)
            ->with('success', 'Pointage enregistré avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(TimeEntry $timeEntry)
    {
        return view('time-entries.show', compact('timeEntry'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TimeEntry $timeEntry)
    {
        $projects = Project::where('status', 'active')->get();
        return view('time-entries.edit', compact('timeEntry', 'projects'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TimeEntry $timeEntry)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'date' => 'required|date',
            'check_in' => 'required|date_format:H:i',
            'check_out' => 'nullable|date_format:H:i|after:check_in',
            'notes' => 'nullable|string',
            'status' => 'required|in:present,absent,late,early_leave',
        ]);

        $timeEntry->update($validated);

        return redirect()->route('time-entries.index')
            ->with('success', 'Pointage mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TimeEntry $timeEntry)
    {
        $timeEntry->delete();

        return redirect()->route('time-entries.index')
            ->with('success', 'Pointage supprimé avec succès.');
    }
}
