<?php

namespace App\Exports;

use App\Models\TimeEntry;
use App\Models\User;
use App\Models\Project;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class ReportsExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $startDate;
    protected $endDate;
    protected $project_id;

    public function __construct($startDate = null, $endDate = null, $project_id = null)
    {
        $this->startDate = $startDate ?? Carbon::now()->startOfMonth();
        $this->endDate = $endDate ?? Carbon::now()->endOfMonth();
        $this->project_id = $project_id;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $query = TimeEntry::with(['user', 'project'])
            ->whereBetween('check_in', [$this->startDate, $this->endDate]);

        if ($this->project_id) {
            $query->where('project_id', $this->project_id);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'Date',
            'Employé',
            'Projet',
            'Heure d\'entrée',
            'Heure de sortie',
            'Heures Totales',
            'Heures Supplémentaires',
            'Statut'
        ];
    }

    public function map($timeEntry): array
    {
        return [
            $timeEntry->check_in->format('d/m/Y'),
            $timeEntry->user->name,
            $timeEntry->project->name,
            $timeEntry->check_in->format('H:i'),
            $timeEntry->check_out->format('H:i'),
            number_format($timeEntry->total_hours, 2),
            number_format($timeEntry->overtime_hours, 2),
            $this->getStatus($timeEntry)
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
            'A1:H1' => [
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E2E8F0']
                ]
            ]
        ];
    }

    private function getStatus($timeEntry)
    {
        $startTime = Carbon::createFromTimeString('08:00:00');
        if ($timeEntry->check_in->format('H:i:s') > $startTime->format('H:i:s')) {
            return 'Retard';
        }
        return 'À l\'heure';
    }
}
