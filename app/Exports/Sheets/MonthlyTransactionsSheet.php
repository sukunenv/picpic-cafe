<?php

namespace App\Exports\Sheets;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MonthlyTransactionsSheet implements FromQuery, WithTitle, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $monthDate; // '2026-05'
    protected $rowNumber = 0;
    protected $period;

    public function __construct($monthDate, $period = 'This Month')
    {
        $this->monthDate = $monthDate;
        $this->period = $period;
    }

    public function query()
    {
        $skipFilter = ($this->period === 'Semua' || $this->period === 'all');

        $query = Order::select(
            'id',
            'order_number',
            'customer_name',
            'total',
            'payment_method',
            DB::raw("CONVERT_TZ(orders.created_at, '+00:00', '+07:00') as created_at_wib")
        )
        ->where('status', 'completed');

        if (!$skipFilter) {
            $start = Carbon::parse($this->monthDate)->startOfMonth();
            $end = Carbon::parse($this->monthDate)->endOfMonth();
            $query->whereBetween('created_at', [$start, $end]);
        }

        return $query->orderBy('orders.created_at', 'asc');
    }

    public function title(): string
    {
        return 'Detail Transaksi';
    }

    public function headings(): array
    {
        return [
            'No',
            'Tanggal',
            'Jam',
            'Order Number',
            'Nama Customer',
            'Metode Bayar',
            'Total'
        ];
    }

    public function map($transaction): array
    {
        $this->rowNumber++;
        $date = Carbon::parse($transaction->created_at_wib);

        return [
            $this->rowNumber,
            $date->format('Y-m-d'),
            $date->format('H:i:s'),
            $transaction->order_number,
            $transaction->customer_name ?: 'Pelanggan',
            strtoupper($transaction->payment_method ?: '-'),
            $transaction->total,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
