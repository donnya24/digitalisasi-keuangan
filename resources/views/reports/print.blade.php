<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .business-name {
            font-size: 24px;
            font-weight: bold;
            color: #2563eb;
        }
        .report-title {
            font-size: 18px;
            margin: 10px 0;
        }
        .summary-cards {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin-bottom: 30px;
        }
        .card {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 15px;
        }
        .card-label {
            font-size: 12px;
            color: #6b7280;
            margin-bottom: 5px;
        }
        .card-value {
            font-size: 16px;
            font-weight: bold;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .table th {
            background: #f3f4f6;
            padding: 10px;
            text-align: left;
            font-size: 12px;
        }
        .table td {
            padding: 8px 10px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 12px;
        }
        .text-right { text-align: right; }
        .text-green { color: #059669; }
        .text-red { color: #dc2626; }
        .text-blue { color: #2563eb; }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 11px;
            color: #9ca3af;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="business-name">{{ $business->business_name ?? 'KeuanganKu' }}</div>
        <div class="report-title">{{ $title }}</div>
        <div>Dicetak: {{ now()->translatedFormat('d F Y H:i') }}</div>
    </div>

    <div class="summary-cards">
        <div class="card">
            <div class="card-label">Total Pemasukan</div>
            <div class="card-value text-green">Rp {{ number_format($summary['total_income'], 0, ',', '.') }}</div>
        </div>
        <div class="card">
            <div class="card-label">Total Pengeluaran</div>
            <div class="card-value text-red">Rp {{ number_format($summary['total_expense'], 0, ',', '.') }}</div>
        </div>
        <div class="card">
            <div class="card-label">Laba Bersih</div>
            <div class="card-value text-blue">Rp {{ number_format($summary['net_profit'], 0, ',', '.') }}</div>
        </div>
        <div class="card">
            <div class="card-label">Jumlah Transaksi</div>
            <div class="card-value">{{ $summary['transaction_count'] }}</div>
        </div>
    </div>

    <h3>Rincian per Kategori</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Kategori</th>
                <th class="text-right">Pemasukan</th>
                <th class="text-right">Pengeluaran</th>
                <th class="text-right">Laba/Rugi</th>
                <th class="text-right">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @foreach($byCategory as $item)
            <tr>
                <td>{{ $item['category'] }}</td>
                <td class="text-right text-green">Rp {{ number_format($item['income'], 0, ',', '.') }}</td>
                <td class="text-right text-red">Rp {{ number_format($item['expense'], 0, ',', '.') }}</td>
                <td class="text-right {{ ($item['profit'] ?? $item['income'] - $item['expense']) >= 0 ? 'text-blue' : 'text-red' }}">
                    Rp {{ number_format(($item['profit'] ?? $item['income'] - $item['expense']), 0, ',', '.') }}
                </td>
                <td class="text-right">{{ $item['count'] }}x</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    @if($type == 'daily' && isset($topTransactions))
    <h3>Transaksi Terbesar</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Deskripsi</th>
                <th>Kategori</th>
                <th class="text-right">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @foreach($topTransactions as $transaction)
            <tr>
                <td>{{ $transaction->description }}</td>
                <td>{{ $transaction->category->name ?? 'Tanpa Kategori' }}</td>
                <td class="text-right {{ $transaction->type == 'pemasukan' ? 'text-green' : 'text-red' }}">
                    {{ $transaction->type == 'pemasukan' ? '+' : '-' }} Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    @if($type == 'monthly' && isset($dailySummaries))
    <h3>Ringkasan Harian</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th class="text-right">Pemasukan</th>
                <th class="text-right">Pengeluaran</th>
                <th class="text-right">Laba</th>
            </tr>
        </thead>
        <tbody>
            @foreach($dailySummaries as $day)
            <tr>
                <td>{{ Carbon\Carbon::parse($day->date)->translatedFormat('d M Y') }}</td>
                <td class="text-right text-green">Rp {{ number_format($day->total_income, 0, ',', '.') }}</td>
                <td class="text-right text-red">Rp {{ number_format($day->total_expense, 0, ',', '.') }}</td>
                <td class="text-right {{ $day->net_profit >= 0 ? 'text-blue' : 'text-red' }}">
                    Rp {{ number_format($day->net_profit, 0, ',', '.') }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <div class="footer">
        Laporan ini digenerate secara otomatis oleh KeuanganKu - {{ config('app.url') }}
    </div>
</body>
</html>