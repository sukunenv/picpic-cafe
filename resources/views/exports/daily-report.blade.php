<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Harian PicPic</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 12px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #766CA9;
        }
        .header p {
            margin: 3px 0;
            color: #555;
            font-size: 12px;
        }
        .narasi {
            background-color: #f8f9fa;
            padding: 12px;
            border-left: 4px solid #766CA9;
            margin-bottom: 20px;
            font-style: italic;
            font-size: 13px;
        }
        .section-title {
            color: #766CA9;
            font-size: 16px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
            margin-bottom: 10px;
            margin-top: 25px;
        }
        .summary-table {
            width: 100%;
            margin-bottom: 20px;
        }
        .summary-table td {
            padding: 5px;
            vertical-align: top;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table.data-table th, table.data-table td {
            padding: 8px 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        table.data-table th {
            background-color: #766CA9;
            color: white;
            font-weight: bold;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .badge {
            padding: 3px 6px;
            background-color: #e2e8f0;
            border-radius: 3px;
            font-size: 10px;
            text-transform: uppercase;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>Kedai PicPic</h1>
        <p>Jl. Kasatrian No.31, Jeruk, Kepek, Wonosari, Gunungkidul, DIY 55813</p>
        <p>Tanggal Laporan: {{ \Carbon\Carbon::parse($date)->translatedFormat('d F Y') }}</p>
        <p><small>Waktu Cetak: {{ \Carbon\Carbon::now('Asia/Jakarta')->format('d M Y, H:i') }} WIB</small></p>
    </div>

    @php
        $topMethod = '-';
        if (count($paymentMethods) > 0) {
            $sortedMethods = collect($paymentMethods)->sortByDesc('total');
            $topMethod = strtoupper($sortedMethods->first()['method']);
        }
    @endphp

    <div class="narasi">
        Hari ini PicPic mencatat <strong>{{ number_format($totalOrders, 0, ',', '.') }}</strong> transaksi dengan total omzet 
        <strong>Rp {{ number_format($totalRevenue, 0, ',', '.') }}</strong>. Metode pembayaran terbanyak: <strong>{{ $topMethod }}</strong>.
    </div>

    <table class="summary-table">
        <tr>
            <td width="33%">
                <strong>Total Omzet:</strong><br>
                <span style="font-size:16px; color:#766CA9; font-weight:bold;">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</span>
            </td>
            <td width="33%">
                <strong>Total Order:</strong><br>
                <span style="font-size:16px; color:#766CA9; font-weight:bold;">{{ number_format($totalOrders, 0, ',', '.') }} Order</span>
            </td>
            <td width="33%">
                <strong>Rata-rata Transaksi:</strong><br>
                <span style="font-size:16px; color:#766CA9; font-weight:bold;">Rp {{ number_format($avgOrder, 0, ',', '.') }}</span>
            </td>
        </tr>
    </table>

    <table width="100%" style="margin-bottom: 20px;">
        <tr>
            <td width="48%" style="vertical-align: top;">
                <div class="section-title" style="margin-top:0;">Top 5 Menu Terlaris</div>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Menu</th>
                            <th class="text-center">Terjual</th>
                            <th class="text-right">Pendapatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topMenus as $menu)
                        <tr>
                            <td>{{ $menu['name'] }}</td>
                            <td class="text-center">{{ $menu['total_sold'] }}</td>
                            <td class="text-right">{{ number_format($menu['revenue'], 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center">Tidak ada data</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </td>
            <td width="4%"></td>
            <td width="48%" style="vertical-align: top;">
                <div class="section-title" style="margin-top:0;">Breakdown Pembayaran</div>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Metode</th>
                            <th class="text-center">Total</th>
                            <th class="text-right">Omzet</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($paymentMethods as $payment)
                        <tr>
                            <td><span class="badge">{{ $payment['method'] }}</span></td>
                            <td class="text-center">{{ $payment['total'] }}</td>
                            <td class="text-right">{{ number_format($payment['revenue'], 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center">Tidak ada data</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </td>
        </tr>
    </table>

    <div class="section-title">Semua Transaksi Hari Ini</div>
    <table class="data-table">
        <thead>
            <tr>
                <th width="5%" class="text-center">No</th>
                <th width="10%">Jam</th>
                <th width="20%">No. Order</th>
                <th width="30%">Nama Customer</th>
                <th width="15%" class="text-center">Metode</th>
                <th width="20%" class="text-right">Total (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $index => $trx)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($trx->created_at)->format('H:i') }}</td>
                <td>{{ $trx->order_number }}</td>
                <td>{{ $trx->customer_name ?: 'Pelanggan' }}</td>
                <td class="text-center">
                    <span class="badge">{{ $trx->payment_method ?: '-' }}</span>
                </td>
                <td class="text-right">{{ number_format($trx->total, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center">Belum ada transaksi hari ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>
