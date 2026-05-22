<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\Exportable;
use Carbon\Carbon;

class MonthlyReportExport implements WithMultipleSheets
{
    use Exportable;

    protected $monthDate; // e.g., '2026-05'
    protected $period;

    public function __construct($monthDate, $period = 'This Month')
    {
        $this->monthDate = $monthDate;
        $this->period = $period;
    }

    public function sheets(): array
    {
        return [
            new Sheets\MonthlySummarySheet($this->monthDate, $this->period),
            new Sheets\MonthlyTransactionsSheet($this->monthDate, $this->period),
            new Sheets\MonthlyTopMenuSheet($this->monthDate, $this->period),
        ];
    }
}
