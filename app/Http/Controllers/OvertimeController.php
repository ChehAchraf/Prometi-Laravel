<?php

namespace App\Http\Controllers;

use App\Models\Overtime;
use App\Models\TimeEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OvertimeController extends Controller
{
    public function index()
    {
        $overtimes = Overtime::with(['user', 'project', 'approver'])
            ->when(Auth::user()->role !== 'admin', function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->latest()
            ->paginate(10);

        return view('overtimes.index', compact('overtimes'));
    }

    public function show(Overtime $overtime)
    {
        $this->authorize('view', $overtime);
        return view('overtimes.show', compact('overtime'));
    }

    public function approve(Request $request, Overtime $overtime)
    {
        $this->authorize('approve', $overtime);

        $request->validate([
            'reason' => 'nullable|string|max:255'
        ]);

        $overtime->approve(Auth::user(), $request->reason);

        return redirect()->route('overtimes.index')
            ->with('success', 'Overtime request approved successfully.');
    }

    public function reject(Request $request, Overtime $overtime)
    {
        $this->authorize('approve', $overtime);

        $request->validate([
            'rejection_reason' => 'required|string|max:255'
        ]);

        $overtime->reject(Auth::user(), $request->rejection_reason);

        return redirect()->route('overtimes.index')
            ->with('success', 'Overtime request rejected successfully.');
    }

    public function store(Request $request, TimeEntry $timeEntry)
    {
        $this->authorize('create', [Overtime::class, $timeEntry]);

        if (!$timeEntry->hasOvertime()) {
            return back()->with('error', 'No overtime hours to request.');
        }

        $overtime = $timeEntry->createOvertimeRequest();

        if (!$overtime) {
            return back()->with('error', 'Failed to create overtime request.');
        }

        return redirect()->route('overtimes.show', $overtime)
            ->with('success', 'Overtime request created successfully.');
    }
} 