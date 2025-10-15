<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Tiket - {{ $pemesanan->kode_pemesanan }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        @page {
            size: 80mm auto;
            margin: 0;
        }
        
        body {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            line-height: 1.4;
            padding: 10mm;
            width: 80mm;
        }
        
        .header {
            text-align: center;
            border-bottom: 2px dashed #000;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }
        
        .header h1 {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .header p {
            font-size: 10px;
        }
        
        .section {
            margin-bottom: 15px;
        }
        
        .section-title {
            font-weight: bold;
            margin-bottom: 5px;
            text-transform: uppercase;
            font-size: 11px;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
        }
        
        .label {
            font-weight: bold;
        }
        
        .value {
            text-align: right;
        }
        
        .booking-code {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            letter-spacing: 2px;
            margin: 15px 0;
            padding: 10px;
            border: 2px solid #000;
            background: #f0f0f0;
        }
        
        .seats {
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            margin: 15px 0;
            padding: 15px;
            border: 3px double #000;
            background: #f9f9f9;
        }
        
        .footer {
            border-top: 2px dashed #000;
            padding-top: 10px;
            margin-top: 15px;
            text-align: center;
            font-size: 10px;
        }
        
        .qr-code {
            text-align: center;
            margin: 15px 0;
        }
        
        .total {
            font-size: 14px;
            font-weight: bold;
            text-align: right;
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px solid #000;
        }
        
        .note {
            font-size: 9px;
            font-style: italic;
            margin-top: 10px;
            padding: 8px;
            border: 1px solid #ccc;
            background: #f9f9f9;
        }
        
        @media print {
            body {
                padding: 5mm;
            }
            
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    
    <!-- Header -->
    <div class="header">
        <h1>🎬 CINEMA POS</h1>
        <p>Jl. Contoh No. 123, Jakarta</p>
        <p>Telp: (021) 1234-5678</p>
    </div>
    
    <!-- Booking Code -->
    <div class="booking-code">
        {{ $pemesanan->kode_pemesanan }}
    </div>
    
    <!-- Film Info -->
    <div class="section">
        <div class="section-title">📽️ Informasi Film</div>
        <div class="info-row">
            <span class="label">Film:</span>
            <span class="value">{{ $pemesanan->jadwal->film->judul }}</span>
        </div>
        <div class="info-row">
            <span class="label">Studio:</span>
            <span class="value">{{ $pemesanan->jadwal->studio->nama_studio }}</span>
        </div>
        <div class="info-row">
            <span class="label">Tanggal:</span>
            <span class="value">{{ date('d M Y', strtotime($pemesanan->jadwal->tanggal_tayang)) }}</span>
        </div>
        <div class="info-row">
            <span class="label">Waktu:</span>
            <span class="value">{{ date('H:i', strtotime($pemesanan->jadwal->jam_tayang)) }} - {{ date('H:i', strtotime($pemesanan->jadwal->waktu_selesai)) }}</span>
        </div>
    </div>
    
    <!-- Seats -->
    <div class="seats">
        KURSI: {{ $pemesanan->kursi->pluck('nomor_kursi')->join(', ') }}
    </div>
    
    <!-- Customer Info -->
    <div class="section">
        <div class="section-title">👤 Informasi Pelanggan</div>
        @php
            $metadata = $pemesanan->metadata ? json_decode($pemesanan->metadata, true) : null;
        @endphp
        
        @if($metadata)
            <div class="info-row">
                <span class="label">Nama:</span>
                <span class="value">{{ $metadata['nama_pelanggan'] ?? $pemesanan->user->nama_lengkap }}</span>
            </div>
            <div class="info-row">
                <span class="label">No. HP:</span>
                <span class="value">{{ $metadata['no_hp_pelanggan'] ?? '-' }}</span>
            </div>
        @else
            <div class="info-row">
                <span class="label">Nama:</span>
                <span class="value">{{ $pemesanan->user->nama_lengkap }}</span>
            </div>
        @endif
    </div>
    
    <!-- Transaction Info -->
    <div class="section">
        <div class="section-title">💳 Informasi Transaksi</div>
        <div class="info-row">
            <span class="label">Tipe:</span>
            <span class="value">{{ $metadata && isset($metadata['tipe']) ? strtoupper($metadata['tipe']) : 'ONLINE' }}</span>
        </div>
        @if($metadata && isset($metadata['metode_bayar']))
        <div class="info-row">
            <span class="label">Pembayaran:</span>
            <span class="value">{{ strtoupper($metadata['metode_bayar']) }}</span>
        </div>
        @endif
        <div class="info-row">
            <span class="label">Tanggal Beli:</span>
            <span class="value">{{ $pemesanan->created_at->format('d M Y H:i') }}</span>
        </div>
        <div class="info-row">
            <span class="label">Check-In:</span>
            <span class="value">{{ $pemesanan->used_at ? $pemesanan->used_at->format('d M Y H:i') : '-' }}</span>
        </div>
        <div class="info-row">
            <span class="label">Petugas:</span>
            <span class="value">{{ $pemesanan->usedBy->name ?? 'System' }}</span>
        </div>
    </div>
    
    <!-- Total -->
    <div class="total">
        TOTAL: Rp {{ number_format($pemesanan->total_harga, 0, ',', '.') }}
    </div>
    
    <!-- Note -->
    <div class="note">
        <strong>PENTING:</strong>
        <br>• Datang 15 menit sebelum film dimulai
        <br>• Simpan tiket ini sampai film selesai
        <br>• Tiket tidak dapat dikembalikan
        <br>• Dilarang membawa makanan/minuman dari luar
    </div>
    
    <!-- Footer -->
    <div class="footer">
        <p>*** TERIMA KASIH ***</p>
        <p>Selamat Menikmati Film</p>
        <p style="margin-top: 10px;">
            Dicetak: {{ now()->format('d M Y H:i:s') }}
        </p>
    </div>
    
    <!-- Print Button (Hidden when printing) -->
    <div class="no-print" style="text-align: center; margin-top: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; font-size: 14px; cursor: pointer; background: #4CAF50; color: white; border: none; border-radius: 5px;">
            🖨️ Cetak Tiket
        </button>
        <button onclick="window.close()" style="padding: 10px 20px; font-size: 14px; cursor: pointer; background: #666; color: white; border: none; border-radius: 5px; margin-left: 10px;">
            Tutup
        </button>
    </div>
    
    <script>
        // Auto print when page loads
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
        };
    </script>
    
</body>
</html>