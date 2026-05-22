<?php

namespace App\Exports\Sheets;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MonthlySummarySheet implements FromCollection, WithTitle, WithHeadings, ShouldAutoSize, WithStyles
{
    protected $monthDate; // '2026-05'
    protected $period;

    public function __construct($monthDate, $period = 'This Month')
    {
        $this->monthDate = $monthDate;
        $this->period = $period;
    }

    public function collection()
    {
        $skipFilter = ($this->period === 'Semua' || $this->period === 'all');

        $query = Order::select(
            DB::raw("DATE(CONVERT_TZ(created_at, '+00:00', '+07:00')) as date"),
            DB::raw("COUNT(id) as total_order"),
            DB::raw("SUM(total) as total_omzet"),
            DB::raw("SUM(CASE WHEN payment_method = 'cash' THEN total ELSE 0 END) as cash"),
            DB::raw("SUM(CASE WHEN payment_method = 'transfer' THEN total ELSE 0 END) as transfer"),
            DB::raw("SUM(CASE WHEN payment_method = 'qris' THEN total ELSE 0 END) as qris")
        )
        ->where('status', 'completed');

        if (!$skipFilter) {
            $start = Carbon::parse($this->monthDate)->startOfMonth();
            $end = Carbon::parse($this->monthDate)->endOfMonth();
            $query->whereBetween('created_at', [$start, $end]);
        }

        $data = $query->groupBy(DB::raw("DATE(CONVERT_TZ(created_at, '+00:00', '+07:00'))"))
            ->orderBy('date', 'asc')
            ->get();

        return $data;
    }

    public function title(): string
    {
        return 'Ringkasan Harian';
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'Total Order',
            'Total Omzet',
            'Cash',
            'Transfer',
            'QRIS'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
