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
            size: 58mm auto;
            margin: 0;
        }
        
        body {
            font-family: 'Courier New', monospace;
            font-size: 10px;
            line-height: 1.3;
            width: 58mm;
            background: white;
        }
        
        /* ✅ TIKET CONTAINER - 1 per halaman */
        .ticket {
            page-break-after: always;
            padding: 8mm 4mm;
            position: relative;
        }
        
        .ticket:last-child {
            page-break-after: avoid;
        }
        
        /* Header */
        .header {
            text-align: center;
            border-bottom: 1px dashed #000;
            padding-bottom: 6px;
            margin-bottom: 8px;
        }
        
        .header h1 {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 2px;
            letter-spacing: 1px;
        }
        
        .header p {
            font-size: 8px;
            color: #333;
        }
        
        /* Booking Code */
        .booking-code {
            text-align: center;
            font-size: 11px;
            font-weight: bold;
            letter-spacing: 1px;
            margin: 8px 0;
            padding: 6px;
            border: 1px solid #000;
            background: #f5f5f5;
        }
        
        /* ✅ BARCODE - SAMA untuk semua tiket */
        .barcode {
            text-align: center;
            margin: 8px 0;
            padding: 4px 0;
            background: white;
        }
        
        .barcode svg {
            width: 100%;
            height: auto;
            max-width: 45mm;
        }
        
        /* Seat Highlight */
        .seat-box {
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            margin: 8px 0;
            padding: 12px;
            border: 2px solid #000;
            background: #fff;
            letter-spacing: 2px;
        }
        
        /* Info Section */
        .info {
            font-size: 9px;
            margin-bottom: 6px;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2px;
        }
        
        .label {
            font-weight: bold;
        }
        
        .value {
            text-align: right;
            max-width: 55%;
            word-wrap: break-word;
        }
        
        /* Divider */
        .divider {
            border-top: 1px dashed #000;
            margin: 6px 0;
        }
        
        /* Footer */
        .footer {
            border-top: 1px dashed #000;
            padding-top: 6px;
            margin-top: 8px;
            text-align: center;
            font-size: 8px;
        }
        
        .note {
            font-size: 7px;
            margin-top: 6px;
            padding: 4px;
            border: 1px solid #ccc;
            background: #fafafa;
            line-height: 1.4;
        }
        
        @media print {
            .no-print {
                display: none !important;
            }
            
            body {
                background: white;
            }
        }
    </style>
</head>
<body>

@php
    $kursiList = $pemesanan->kursi;
    $metadata = $pemesanan->metadata ? json_decode($pemesanan->metadata, true) : null;
    $namaPelanggan = $metadata['nama_pelanggan'] ?? $pemesanan->user->nama_lengkap ?? 'Pelanggan';
    $bookingCode = $pemesanan->kode_pemesanan;
@endphp

{{-- ✅ LOOP: 1 TIKET PER KURSI (tapi barcode sama semua) --}}
@foreach($kursiList as $index => $kursi)
<div class="ticket">
    
    <!-- Header -->
    <div class="header">
        <h1>🎬 HAPPYCINE</h1>
        <p>Cinema Experience</p>
    </div>
    
    <!-- Booking Code -->
    <div class="booking-code">
        {{ $bookingCode }}
    </div>
    
    <!-- ✅ BARCODE - SAMA untuk semua tiket -->
    <div class="barcode">
        <svg id="barcode-{{ $index }}"></svg>
    </div>
    
    <!-- Seat Number (BIG) - BEDA tiap tiket -->
    <div class="seat-box">
        {{ $kursi->nomor_kursi }}
    </div>
    
    <!-- Film Info -->
    <div class="info">
        <div class="info-row">
            <span class="label">Film:</span>
            <span class="value">{{ Str::limit($pemesanan->jadwal->film->judul, 25) }}</span>
        </div>
        <div class="info-row">
            <span class="label">Studio:</span>
            <span class="value">{{ $pemesanan->jadwal->studio->nama_studio }}</span>
        </div>
        <div class="info-row">
            <span class="label">Tanggal:</span>
            <span class="value">{{ \Carbon\Carbon::parse($pemesanan->jadwal->tanggal_tayang)->format('d M Y') }}</span>
        </div>
        <div class="info-row">
            <span class="label">Jam:</span>
            <span class="value">{{ \Carbon\Carbon::parse($pemesanan->jadwal->jam_tayang)->format('H:i') }} WIB</span>
        </div>
    </div>
    
    <div class="divider"></div>
    
    <!-- Customer -->
    <div class="info">
        <div class="info-row">
            <span class="label">Nama:</span>
            <span class="value">{{ Str::limit($namaPelanggan, 20) }}</span>
        </div>
        <div class="info-row">
            <span class="label">Tipe:</span>
            <span class="value">{{ $metadata && isset($metadata['tipe']) ? strtoupper($metadata['tipe']) : 'ONLINE' }}</span>
        </div>
    </div>
    
    <div class="divider"></div>
    
    <!-- Total -->
    <div class="info">
        <div class="info-row">
            <span class="label">Harga:</span>
            <span class="value">Rp {{ number_format($kursi->harga ?? ($pemesanan->total_bayar / $kursiList->count()), 0, ',', '.') }}</span>
        </div>
    </div>
    
    <!-- Note -->
    <div class="note">
        <strong>PENTING:</strong> Datang 15 menit sebelum film. Tunjukkan tiket ke petugas. Tiket tidak dapat dikembalikan.
    </div>
    
    <!-- Footer -->
    <div class="footer">
        <p>*** TERIMA KASIH ***</p>
        <p>Selamat Menikmati Film</p>
        <p style="margin-top: 4px; font-size: 7px;">
            Tiket {{ $index + 1 }} dari {{ $kursiList->count() }} | {{ now()->format('d/m/Y H:i') }}
        </p>
    </div>
    
</div>
@endforeach

<!-- Print Controls -->
<div class="no-print" style="position: fixed; bottom: 20px; left: 50%; transform: translateX(-50%); z-index: 1000; background: white; padding: 15px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
    <button onclick="window.print()" style="padding: 10px 20px; font-size: 14px; cursor: pointer; background: #4CAF50; color: white; border: none; border-radius: 5px; margin-right: 10px; font-weight: bold;">
        🖨️ Cetak {{ $kursiList->count() }} Tiket
    </button>
    <button onclick="window.close()" style="padding: 10px 20px; font-size: 14px; cursor: pointer; background: #666; color: white; border: none; border-radius: 5px;">
        Tutup
    </button>
</div>

<!-- JsBarcode Library -->
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>

<script>
    // ✅ Generate BARCODE YANG SAMA untuk semua tiket
    const bookingCode = '{{ $bookingCode }}';
    const kursiCount = {{ $kursiList->count() }};
    
    // Loop generate barcode untuk setiap tiket (tapi isinya sama)
    for (let i = 0; i < kursiCount; i++) {
        JsBarcode(`#barcode-${i}`, bookingCode, {
            format: "CODE128",
            width: 1.5,
            height: 40,
            displayValue: true,
            fontSize: 10,
            margin: 0,
            background: "#ffffff"
        });
    }
    
    // Auto print setelah 500ms
    window.onload = function() {
        setTimeout(() => {
            window.print();
        }, 500);
    };
</script>

</body>
</html>