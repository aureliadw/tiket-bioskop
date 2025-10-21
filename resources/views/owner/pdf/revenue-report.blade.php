<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Revenue - HappyCine</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #1f2937;
        }
        
        .container {
            max-width: 100%;
            padding: 20px;
        }
        
        /* Header */
        .header {
            text-align: center;
            border-bottom: 3px solid #7c3aed;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .logo {
            font-size: 32px;
            margin-bottom: 5px;
        }
        
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #7c3aed;
            margin-bottom: 5px;
        }
        
        .report-title {
            font-size: 18px;
            font-weight: bold;
            color: #374151;
            margin-top: 10px;
        }
        
        .report-period {
            font-size: 14px;
            color: #6b7280;
            margin-top: 5px;
        }
        
        /* Info Section */
        .info-section {
            margin-bottom: 25px;
            background-color: #f9fafb;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #7c3aed;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }
        
        .info-label {
            font-weight: 600;
            color: #374151;
        }
        
        .info-value {
            color: #1f2937;
        }
        
        /* Summary Cards */
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 30px;
        }
        
        .summary-card {
            background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%);
            color: white;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
        }
        
        .summary-card.green {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }
        
        .summary-card.blue {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        }
        
        .summary-card.orange {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        }
        
        .summary-label {
            font-size: 11px;
            opacity: 0.9;
            margin-bottom: 5px;
        }
        
        .summary-value {
            font-size: 18px;
            font-weight: bold;
        }
        
        /* Channel Breakdown */
        .channel-section {
            margin-bottom: 30px;
            background-color: #f9fafb;
            padding: 15px;
            border-radius: 8px;
        }
        
        .channel-section h3 {
            font-size: 14px;
            font-weight: bold;
            color: #374151;
            margin-bottom: 15px;
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 8px;
        }
        
        .channel-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }
        
        .channel-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px;
            background-color: white;
            border-radius: 6px;
            border: 1px solid #e5e7eb;
        }
        
        .channel-label {
            display: flex;
            align-items: center;
            font-weight: 600;
            color: #374151;
        }
        
        .channel-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 8px;
        }
        
        .channel-dot.online {
            background-color: #3b82f6;
        }
        
        .channel-dot.offline {
            background-color: #9ca3af;
        }
        
        .channel-value {
            text-align: right;
        }
        
        .channel-amount {
            font-weight: bold;
            font-size: 14px;
            color: #1f2937;
        }
        
        .channel-percentage {
            font-size: 11px;
            color: #6b7280;
        }
        
        /* Table */
        .table-section {
            margin-bottom: 30px;
        }
        
        .table-title {
            font-size: 16px;
            font-weight: bold;
            color: #374151;
            margin-bottom: 15px;
            border-bottom: 2px solid #7c3aed;
            padding-bottom: 8px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
        }
        
        thead {
            background-color: #f3f4f6;
        }
        
        th {
            padding: 12px 8px;
            text-align: left;
            font-weight: 600;
            font-size: 11px;
            color: #6b7280;
            text-transform: uppercase;
            border-bottom: 2px solid #e5e7eb;
        }
        
        th.text-right {
            text-align: right;
        }
        
        td {
            padding: 10px 8px;
            border-bottom: 1px solid #f3f4f6;
            font-size: 11px;
        }
        
        td.text-right {
            text-align: right;
        }
        
        tbody tr:hover {
            background-color: #f9fafb;
        }
        
        .day-name {
            font-size: 10px;
            color: #9ca3af;
        }
        
        .amount-main {
            font-weight: 600;
            color: #1f2937;
        }
        
        .amount-online {
            color: #3b82f6;
        }
        
        .amount-offline {
            color: #6b7280;
        }
        
        /* Footer Total */
        tfoot {
            background-color: #f9fafb;
            font-weight: bold;
        }
        
        tfoot td {
            padding: 12px 8px;
            border-top: 2px solid #7c3aed;
            font-size: 12px;
            color: #1f2937;
        }
        
        /* Footer */
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #e5e7eb;
            text-align: center;
            font-size: 11px;
            color: #6b7280;
        }
        
        .signature-section {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 50px;
            margin-top: 50px;
            margin-bottom: 20px;
        }
        
        .signature-box {
            text-align: center;
        }
        
        .signature-label {
            font-size: 12px;
            color: #6b7280;
            margin-bottom: 60px;
        }
        
        .signature-name {
            font-weight: bold;
            color: #1f2937;
            border-top: 1px solid #1f2937;
            padding-top: 5px;
            display: inline-block;
            min-width: 200px;
        }
        
        /* Page Break */
        .page-break {
            page-break-after: always;
        }
        
        /* Print Styles */
        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .page-break {
                page-break-after: always;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="logo">🎬</div>
            <div class="company-name">HAPPYCINE</div>
            <div class="report-title">LAPORAN REVENUE DETAIL</div>
            <div class="report-period">
                Periode: {{ \Carbon\Carbon::parse($startDate)->format('d F Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d F Y') }}
            </div>
        </div>

        <!-- Info Section -->
        <div class="info-section">
            <div class="info-row">
                <span class="info-label">Tanggal Cetak:</span>
                <span class="info-value">{{ now()->format('d F Y, H:i') }} WIB</span>
            </div>
            <div class="info-row">
                <span class="info-label">Dicetak Oleh:</span>
                <span class="info-value">{{ auth()->user()->nama_lengkap }} (Owner)</span>
            </div>
            <div class="info-row">
                <span class="info-label">Total Hari:</span>
                <span class="info-value">{{ \Carbon\Carbon::parse($startDate)->diffInDays(\Carbon\Carbon::parse($endDate)) + 1 }} hari</span>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="summary-grid">
            <div class="summary-card">
                <div class="summary-label">Total Revenue</div>
                <div class="summary-value">Rp {{ number_format($summary['total_revenue'], 0, ',', '.') }}</div>
            </div>
            <div class="summary-card green">
                <div class="summary-label">Total Kursi Terjual</div>
                <div class="summary-value">{{ number_format($summary['total_tickets']) }}</div>
            </div>
            <div class="summary-card blue">
                <div class="summary-label">Total Transaksi</div>
                <div class="summary-value">{{ number_format($summary['total_transactions']) }}</div>
            </div>
            <div class="summary-card orange">
                <div class="summary-label">Rata-rata Transaksi</div>
                <div class="summary-value">Rp {{ number_format($summary['avg_transaction'], 0, ',', '.') }}</div>
            </div>
        </div>

        <!-- Channel Breakdown -->
        <div class="channel-section">
            <h3>Breakdown Channel Penjualan</h3>
            <div class="channel-grid">
                <div class="channel-item">
                    <div class="channel-label">
                        <span class="channel-dot online"></span>
                        Online
                    </div>
                    <div class="channel-value">
                        <div class="channel-amount">Rp {{ number_format($summary['online_revenue'], 0, ',', '.') }}</div>
                        <div class="channel-percentage">
                            {{ $summary['total_revenue'] > 0 ? number_format(($summary['online_revenue'] / $summary['total_revenue']) * 100, 1) : 0 }}%
                        </div>
                    </div>
                </div>
                <div class="channel-item">
                    <div class="channel-label">
                        <span class="channel-dot offline"></span>
                        Offline
                    </div>
                    <div class="channel-value">
                        <div class="channel-amount">Rp {{ number_format($summary['offline_revenue'], 0, ',', '.') }}</div>
                        <div class="channel-percentage">
                            {{ $summary['total_revenue'] > 0 ? number_format(($summary['offline_revenue'] / $summary['total_revenue']) * 100, 1) : 0 }}%
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Daily Revenue Table -->
        <div class="table-section">
            <div class="table-title">Revenue Harian</div>
            <table>
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th class="text-right">Total Revenue</th>
                        <th class="text-right">Online</th>
                        <th class="text-right">Offline</th>
                        <th class="text-right">Kursi</th>
                        <th class="text-right">Transaksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($dailyRevenue as $day)
                    <tr>
                        <td>
                            <div>{{ \Carbon\Carbon::parse($day->date)->format('d M Y') }}</div>
                            <div class="day-name">{{ \Carbon\Carbon::parse($day->date)->locale('id')->isoFormat('dddd') }}</div>
                        </td>
                        <td class="text-right">
                            <span class="amount-main">Rp {{ number_format($day->revenue, 0, ',', '.') }}</span>
                        </td>
                        <td class="text-right">
                            <span class="amount-online">Rp {{ number_format($day->online_revenue, 0, ',', '.') }}</span>
                        </td>
                        <td class="text-right">
                            <span class="amount-offline">Rp {{ number_format($day->offline_revenue, 0, ',', '.') }}</span>
                        </td>
                        <td class="text-right">{{ number_format($day->tickets) }}</td>
                        <td class="text-right">{{ number_format($day->transactions) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 20px; color: #9ca3af;">
                            Tidak ada data untuk periode ini
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                @if($dailyRevenue->count() > 0)
                <tfoot>
                    <tr>
                        <td>TOTAL</td>
                        <td class="text-right">Rp {{ number_format($summary['total_revenue'], 0, ',', '.') }}</td>
                        <td class="text-right">Rp {{ number_format($summary['online_revenue'], 0, ',', '.') }}</td>
                        <td class="text-right">Rp {{ number_format($summary['offline_revenue'], 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($summary['total_tickets']) }}</td>
                        <td class="text-right">{{ number_format($summary['total_transactions']) }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>

        <!-- Signature Section -->
        <div class="signature-section">
            <div class="signature-box">
                <div class="signature-label">Mengetahui,</div>
                <div class="signature-name">Owner</div>
            </div>
            <div class="signature-box">
                <div class="signature-label">Dicetak Oleh,</div>
                <div class="signature-name">{{ auth()->user()->nama_lengkap }}</div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><strong>HappyCine</strong> - Laporan Revenue Otomatis</p>
            <p>Dokumen ini dicetak secara otomatis dari sistem pada {{ now()->format('d F Y, H:i') }} WIB</p>
        </div>
    </div>
</body>
</html>