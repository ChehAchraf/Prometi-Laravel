<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\Project;
use App\Models\TimeEntry;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exports\ReportsExport;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            // Check if user can view reports (superadmin, hr_editor, chef_de_projet)
            if (!auth()->user()->canViewReports()) {
                abort(403, 'Unauthorized action.');
            }
            return $next($request);
        });
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();
        $previousMonth = $now->copy()->subMonth();
        $startOfPreviousMonth = $previousMonth->copy()->startOfMonth();
        $endOfPreviousMonth = $previousMonth->copy()->endOfMonth();

        // Calculate current month statistics
        $currentMonthStats = $this->calculateMonthlyStats($startOfMonth, $endOfMonth);

        // Calculate previous month statistics for comparison
        $previousMonthStats = $this->calculateMonthlyStats($startOfPreviousMonth, $endOfPreviousMonth);

        // Get projects data
        $projects = Project::with(['chef', 'timeEntries'])->withCount('employees')->get();

        // Get data for charts
        $projectNames = $projects->pluck('name');
        $projectHours = $projects->map(function ($project) {
            return $project->total_hours;
        });

        // Get last 6 months data for evolution chart
        $monthLabels = [];
        $regularHours = [];
        $overtimeHoursData = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = $now->copy()->subMonths($i);
            $monthLabels[] = $date->format('F');

            $entries = TimeEntry::whereYear('check_in', $date->year)
                ->whereMonth('check_in', $date->month)
                ->get();

            $regularHours[] = $entries->sum('total_hours') - $entries->sum('overtime_hours');
            $overtimeHoursData[] = $entries->sum('overtime_hours');
        }

        // Calculate percentage changes
        $stats = [
            'totalHours' => [
                'current' => $currentMonthStats['totalHours'],
                'previous' => $previousMonthStats['totalHours'],
                'change' => $this->calculatePercentageChange(
                    $previousMonthStats['totalHours'],
                    $currentMonthStats['totalHours']
                )
            ],
            'overtimeHours' => [
                'current' => $currentMonthStats['overtimeHours'],
                'previous' => $previousMonthStats['overtimeHours'],
                'change' => $this->calculatePercentageChange(
                    $previousMonthStats['overtimeHours'],
                    $currentMonthStats['overtimeHours']
                )
            ],
            'activeEmployees' => [
                'current' => $currentMonthStats['activeEmployees'],
                'previous' => $previousMonthStats['activeEmployees'],
                'change' => $this->calculatePercentageChange(
                    $previousMonthStats['activeEmployees'],
                    $currentMonthStats['activeEmployees']
                )
            ],
            'absenceRate' => [
                'current' => $currentMonthStats['absenceRate'],
                'previous' => $previousMonthStats['absenceRate'],
                'change' => $previousMonthStats['absenceRate'] - $currentMonthStats['absenceRate']
            ]
        ];

        // Get top performers
        $topPerformers = User::select('users.*')
            ->join('time_entries', 'users.id', '=', 'time_entries.user_id')
            ->join('projects', 'time_entries.project_id', '=', 'projects.id')
            ->whereBetween('time_entries.check_in', [$startOfMonth, $endOfMonth])
            ->groupBy('users.id')
            ->selectRaw('
                SUM(time_entries.total_hours) as total_hours,
                SUM(time_entries.overtime_hours) as overtime_hours,
                MAX(projects.name) as project_name
            ')
            ->orderBy('total_hours', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($employee) {
                $employee->role = ['Maçon', 'Électricien', 'Plombier', 'Peintre', 'Architecte'][rand(0, 4)];
                $employee->performance = rand(80, 100);
                $employee->avatar_url = "https://ui-avatars.com/api/?name=" . urlencode($employee->name) . "&background=random";
                return $employee;
            });

        return view('reports.index', compact(
            'stats',
            'projects',
            'topPerformers',
            'projectNames',
            'projectHours',
            'monthLabels',
            'regularHours',
            'overtimeHoursData'
        ));
    }

    private function calculateMonthlyStats($startDate, $endDate)
    {
        // Get total working days in the month (excluding weekends)
        $workingDays = $this->getWorkingDaysCount($startDate, $endDate);

        // Get all employees excluding admins
        $employees = User::where('role', '!=', 'admin')->get();
        $totalEmployees = $employees->count();

        // Calculate time entries statistics
        $timeEntries = TimeEntry::whereBetween('check_in', [$startDate, $endDate])->get();

        $totalHours = $timeEntries->sum('total_hours');
        $overtimeHours = $timeEntries->sum('overtime_hours');

        // Calculate active employees (employees who have at least one time entry)
        $activeEmployees = $timeEntries->pluck('user_id')->unique()->count();

        // Calculate absence rate more accurately by considering each employee's attendance
        $totalExpectedDays = 0;
        $totalAbsentDays = 0;
        
        // Get all dates in the range that are working days
        $workingDateRange = [];
        $currentDate = $startDate->copy();
        while ($currentDate->lte($endDate)) {
            // Check if it's not a weekend (Saturday = 6, Sunday = 0)
            if ($currentDate->dayOfWeek !== 0 && $currentDate->dayOfWeek !== 6) {
                $workingDateRange[] = $currentDate->format('Y-m-d');
            }
            $currentDate->addDay();
        }
        
        // Count total absences across all employees
        foreach ($employees as $employee) {
            // Each employee is expected to work all working days
            $employeeExpectedDays = count($workingDateRange);
            $totalExpectedDays += $employeeExpectedDays;
            
            // Get the dates this employee was present
            $presentDates = $timeEntries
                ->where('user_id', $employee->id)
                ->map(function ($entry) {
                    return Carbon::parse($entry->check_in)->format('Y-m-d');
                })
                ->unique()
                ->values()
                ->toArray();
            
            // Count absences for this employee (working days they weren't present)
            $employeeAbsentDays = 0;
            foreach ($workingDateRange as $workDate) {
                if (!in_array($workDate, $presentDates)) {
                    $employeeAbsentDays++;
                }
            }
            
            $totalAbsentDays += $employeeAbsentDays;
        }
        
        // Calculate the absence rate as a percentage, but scale it to be more reasonable
        // If there's only one absence out of many expected days, the percentage should be low
        $absenceRate = $totalExpectedDays > 0
            ? ($totalAbsentDays / $totalExpectedDays) * 100
            : 0;

        return [
            'totalHours' => round($totalHours, 1),
            'overtimeHours' => round($overtimeHours, 1),
            'activeEmployees' => $activeEmployees,
            'absenceRate' => round($absenceRate, 1)
        ];
    }

    private function getWorkingDaysCount($startDate, $endDate)
    {
        $workingDays = 0;
        $currentDate = $startDate->copy();

        while ($currentDate->lte($endDate)) {
            // Check if it's not a weekend (Saturday = 6, Sunday = 0)
            if ($currentDate->dayOfWeek !== 0 && $currentDate->dayOfWeek !== 6) {
                $workingDays++;
            }
            $currentDate->addDay();
        }

        return $workingDays;
    }

    private function calculatePercentageChange($oldValue, $newValue)
    {
        if ($oldValue == 0) {
            return $newValue > 0 ? 100 : 0;
        }

        return round((($newValue - $oldValue) / $oldValue) * 100, 1);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $projects = Project::where('status', 'active')->get();
        return view('reports.create', compact('projects'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'report_type' => 'required|in:daily,weekly,monthly',
        ]);

        // Get attendance data for the period
        $timeEntries = TimeEntry::where('project_id', $validated['project_id'])
            ->whereBetween('date', [$validated['start_date'], $validated['end_date']])
            ->get();

        $attendanceData = $this->generateAttendanceData($timeEntries, $validated['report_type']);

        $validated['attendance_data'] = $attendanceData;
        $validated['summary'] = $this->generateSummary($attendanceData);

        Report::create($validated);

        return redirect()->route('reports.index')
            ->with('success', 'Rapport généré avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Report $report)
    {
        return view('reports.show', compact('report'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    private function generateAttendanceData($timeEntries, $reportType)
    {
        $data = [];

        foreach ($timeEntries as $entry) {
            $date = $entry->date->format('Y-m-d');
            if (!isset($data[$date])) {
                $data[$date] = [
                    'present' => 0,
                    'absent' => 0,
                    'late' => 0,
                    'early_leave' => 0,
                ];
            }
            $data[$date][$entry->status]++;
        }

        return $data;
    }

    private function generateSummary($attendanceData)
    {
        $total = 0;
        $present = 0;
        $absent = 0;
        $late = 0;
        $earlyLeave = 0;

        foreach ($attendanceData as $day) {
            $total++;
            $present += $day['present'];
            $absent += $day['absent'];
            $late += $day['late'];
            $earlyLeave += $day['early_leave'];
        }

        return "Résumé: {$present} présents, {$absent} absents, {$late} retards, {$earlyLeave} départs anticipés sur {$total} jours.";
    }

    public function export(Request $request)
    {
        $validated = $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'project_id' => 'nullable|exists:projects,id'
        ]);

        $startDate = isset($validated['start_date']) ? Carbon::parse($validated['start_date']) : null;
        $endDate = isset($validated['end_date']) ? Carbon::parse($validated['end_date']) : null;
        $projectId = $validated['project_id'] ?? null;

        return Excel::download(
            new ReportsExport($startDate, $endDate, $projectId),
            'rapport-' . now()->format('Y-m-d') . '.xlsx'
        );
    }
}
