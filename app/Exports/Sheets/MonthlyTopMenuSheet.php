<?php

namespace App\Exports\Sheets;

use App\Models\OrderItem;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MonthlyTopMenuSheet implements FromCollection, WithTitle, WithHeadings, ShouldAutoSize, WithStyles
{
    protected $monthDate;
    protected $period;

    public function __construct($monthDate, $period = 'This Month')
    {
        $this->monthDate = $monthDate;
        $this->period = $period;
    }

    public function collection()
    {
        $skipFilter = ($this->period === 'Semua' || $this->period === 'all');

        $query = OrderItem::select(
                'menus.name',
                DB::raw('SUM(order_items.quantity) as total_sold'),
                DB::raw('SUM(order_items.subtotal) as revenue')
            )
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('menus', 'order_items.menu_id', '=', 'menus.id')
            ->whereIn('orders.status', ['completed']);

        if (!$skipFilter) {
            $start = Carbon::parse($this->monthDate)->startOfMonth();
            $end = Carbon::parse($this->monthDate)->endOfMonth();
            $query->whereBetween('orders.created_at', [$start, $end]);
        }

        $data = $query->groupBy('menus.id', 'menus.name')
            ->orderBy('total_sold', 'desc')
            ->get();

        $rankedData = collect([]);
        foreach ($data as $index => $item) {
            $rankedData->push([
                'rank' => $index + 1,
                'name' => $item->name,
                'total_sold' => $item->total_sold,
                'revenue' => $item->revenue
            ]);
        }

        return $rankedData;
    }

    public function title(): string
    {
        return 'Top Menu';
    }

    public function headings(): array
    {
        return [
            'Rank',
            'Nama Menu',
            'Jumlah Terjual',
            'Total Pendapatan'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
