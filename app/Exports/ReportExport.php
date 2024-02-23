<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Illuminate\Support\Carbon;
use App\Models\Attendance;
use App\Models\User;
use App\Models\Employee;

class ReportExport implements FromCollection
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate, $endDate)
    {
        $this->startDate = Carbon::parse($startDate);
        $this->endDate = Carbon::parse($endDate);
    }


    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        //
        return Attendance::join('employees', 'employees.id', '=', 'attendances.employee_id')
        ->join('users', 'users.id', '=', 'employees.user_id')
        ->whereBetween('attendances.created_at', [$this->startDate, $this->endDate])
        ->select('employees.emp_id', 'users.first_name', 'users.last_name', 'attendances.start_time', 'attendances.end_time')
        ->orderBy('attendances.created_at', 'desc')
        ->get();
    }

    public function headings(): array
    {
        return [
            'Employee ID',
            'First Name',
            'Last Name',
            'Start Time',
            'End Time',
        ];
    }

    public function contentType(): string
    {
        return 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
    }

    public function withResponse($response)
    {
        $response->headers->set('Content-Disposition', 'attachment;filename=attendance.xlsx');
        $response->headers->set('Cache-Control', 'max-age=0');
    }
}
