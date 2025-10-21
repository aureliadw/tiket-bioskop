<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pemesanan;
use App\Models\Pembayaran;
use App\Models\Film;
use App\Models\Jadwal;
use App\Models\Studio;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class OwnerController extends Controller
{
    /**
     * Executive Dashboard - Overview semua metrics
     */
    public function index()
    {
        // ===== TODAY'S METRICS =====
        $today = Carbon::today();
        
        // Today Revenue - dari pemesanan yang sudah bayar
        $todayRevenue = Pemesanan::whereDate('tanggal_bayar', $today)
            ->where('status_pembayaran', 'sudah_bayar')
            ->sum('total_bayar');
        
        // Today Tickets
        $todayTickets = Pemesanan::whereDate('tanggal_bayar', $today)
            ->where('status_pembayaran', 'sudah_bayar')
            ->sum('jumlah_kursi');

        // ===== THIS WEEK METRICS =====
        $weekStart = Carbon::now()->startOfWeek();
        $weekRevenue = Pemesanan::whereBetween('tanggal_bayar', [$weekStart, now()])
            ->where('status_pembayaran', 'sudah_bayar')
            ->sum('total_bayar');
        
        $weekTickets = Pemesanan::whereBetween('tanggal_bayar', [$weekStart, now()])
            ->where('status_pembayaran', 'sudah_bayar')
            ->sum('jumlah_kursi');

        // ===== THIS MONTH METRICS =====
        $monthStart = Carbon::now()->startOfMonth();
        $monthRevenue = Pemesanan::whereBetween('tanggal_bayar', [$monthStart, now()])
            ->where('status_pembayaran', 'sudah_bayar')
            ->sum('total_bayar');
        
        $monthTickets = Pemesanan::whereBetween('tanggal_bayar', [$monthStart, now()])
            ->where('status_pembayaran', 'sudah_bayar')
            ->sum('jumlah_kursi');

        // ===== THIS YEAR METRICS =====
        $yearStart = Carbon::now()->startOfYear();
        $yearRevenue = Pemesanan::whereBetween('tanggal_bayar', [$yearStart, now()])
            ->where('status_pembayaran', 'sudah_bayar')
            ->sum('total_bayar');
        
        $yearTickets = Pemesanan::whereBetween('tanggal_bayar', [$yearStart, now()])
            ->where('status_pembayaran', 'sudah_bayar')
            ->sum('jumlah_kursi');

        // ===== REVENUE TREND (LAST 30 DAYS) =====
        $revenueTrend = Pemesanan::select(
                DB::raw('DATE(tanggal_bayar) as date'),
                DB::raw('SUM(total_bayar) as revenue')
            )
            ->where('status_pembayaran', 'sudah_bayar')
            ->whereNotNull('tanggal_bayar')
            ->whereBetween('tanggal_bayar', [now()->subDays(30), now()])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // ===== TOP PERFORMING FILMS (THIS MONTH) =====
        $topFilms = Pemesanan::select(
                'films.judul',
                DB::raw('SUM(pemesanans.total_bayar) as revenue'),
                DB::raw('SUM(pemesanans.jumlah_kursi) as tickets'),
                DB::raw('COUNT(DISTINCT pemesanans.id) as transactions')
            )
            ->join('jadwals', 'pemesanans.jadwal_id', '=', 'jadwals.id')
            ->join('films', 'jadwals.film_id', '=', 'films.id')
            ->where('pemesanans.status_pembayaran', 'sudah_bayar')
            ->whereNotNull('pemesanans.tanggal_bayar')
            ->whereBetween('pemesanans.tanggal_bayar', [$monthStart, now()])
            ->groupBy('films.id', 'films.judul')
            ->orderBy('revenue', 'desc')
            ->limit(5)
            ->get();

        // ===== OCCUPANCY RATE PER STUDIO (THIS MONTH) =====
        $occupancyRates = DB::table('jadwals')
            ->select(
                'studios.nama_studio',
                'studios.total_kursi',
                DB::raw('COALESCE(SUM(pemesanans.jumlah_kursi), 0) as tickets_sold'),
                DB::raw('COUNT(DISTINCT jadwals.id) as total_shows'),
                DB::raw('ROUND(
                    (COALESCE(SUM(pemesanans.jumlah_kursi), 0) / 
                    (COUNT(DISTINCT jadwals.id) * studios.total_kursi)) * 100, 
                    2
                ) as occupancy_rate')
            )
            ->join('studios', 'jadwals.studio_id', '=', 'studios.id')
            ->leftJoin('pemesanans', function($join) {
                $join->on('jadwals.id', '=', 'pemesanans.jadwal_id')
                     ->where('pemesanans.status_pembayaran', '=', 'sudah_bayar');
            })
            ->whereBetween('jadwals.tanggal_tayang', [$monthStart->toDateString(), now()->toDateString()])
            ->where('jadwals.status_aktif', true)
            ->groupBy('studios.id', 'studios.nama_studio', 'studios.total_kursi')
            ->get();

        // ===== ONLINE VS OFFLINE BREAKDOWN (THIS MONTH) =====
        $onlineRevenue = Pemesanan::where('status_pembayaran', 'sudah_bayar')
            ->whereNotNull('tanggal_bayar')
            ->whereBetween('tanggal_bayar', [$monthStart, now()])
            ->where('tipe_pemesanan', 'online')
            ->sum('total_bayar');
        
        $offlineRevenue = Pemesanan::where('status_pembayaran', 'sudah_bayar')
            ->whereNotNull('tanggal_bayar')
            ->whereBetween('tanggal_bayar', [$monthStart, now()])
            ->where('tipe_pemesanan', 'offline')
            ->sum('total_bayar');

        // ===== ADDITIONAL METRICS =====
        // Average ticket price
        $avgTicketPrice = $monthTickets > 0 ? $monthRevenue / $monthTickets : 0;

        // Total customers this month
        $totalCustomers = Pemesanan::whereBetween('tanggal_bayar', [$monthStart, now()])
            ->where('status_pembayaran', 'sudah_bayar')
            ->distinct('user_id')
            ->count('user_id');

        return view('owner.dashboard', compact(
            'todayRevenue', 'todayTickets',
            'weekRevenue', 'weekTickets',
            'monthRevenue', 'monthTickets',
            'yearRevenue', 'yearTickets',
            'revenueTrend',
            'topFilms',
            'occupancyRates',
            'onlineRevenue',
            'offlineRevenue',
            'avgTicketPrice',
            'totalCustomers'
        ));
    }

    /**
     * Revenue Report dengan filter
     */
    public function revenueReport(Request $request)
    {
        // Filter date range
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());

        // Daily breakdown
        $dailyRevenue = Pemesanan::select(
                DB::raw('DATE(tanggal_bayar) as date'),
                DB::raw('SUM(total_bayar) as revenue'),
                DB::raw('SUM(jumlah_kursi) as tickets'),
                DB::raw('COUNT(*) as transactions'),
                DB::raw('SUM(CASE WHEN tipe_pemesanan = "online" THEN total_bayar ELSE 0 END) as online_revenue'),
                DB::raw('SUM(CASE WHEN tipe_pemesanan = "offline" THEN total_bayar ELSE 0 END) as offline_revenue')
            )
            ->where('status_pembayaran', 'sudah_bayar')
            ->whereNotNull('tanggal_bayar')
            ->whereBetween('tanggal_bayar', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->get();

        // Summary totals
        $summary = [
            'total_revenue' => $dailyRevenue->sum('revenue'),
            'total_tickets' => $dailyRevenue->sum('tickets'),
            'total_transactions' => $dailyRevenue->sum('transactions'),
            'online_revenue' => $dailyRevenue->sum('online_revenue'),
            'offline_revenue' => $dailyRevenue->sum('offline_revenue'),
            'avg_transaction' => $dailyRevenue->sum('transactions') > 0 
                ? $dailyRevenue->sum('revenue') / $dailyRevenue->sum('transactions') 
                : 0,
        ];

        return view('owner.revenue-report', compact('dailyRevenue', 'summary', 'startDate', 'endDate'));
    }

    /**
     * Film Performance Analysis
     */
    public function filmPerformance(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());

        $filmPerformance = Pemesanan::select(
                'films.id',
                'films.judul',
                'films.genre',
                'films.poster_image',
                DB::raw('SUM(pemesanans.total_bayar) as total_revenue'),
                DB::raw('SUM(pemesanans.jumlah_kursi) as total_tickets'),
                DB::raw('COUNT(DISTINCT pemesanans.id) as total_transactions'),
                DB::raw('COUNT(DISTINCT jadwals.id) as total_shows'),
                DB::raw('ROUND(AVG(pemesanans.jumlah_kursi), 2) as avg_tickets_per_booking')
            )
            ->join('jadwals', 'pemesanans.jadwal_id', '=', 'jadwals.id')
            ->join('films', 'jadwals.film_id', '=', 'films.id')
            ->where('pemesanans.status_pembayaran', 'sudah_bayar')
            ->whereNotNull('pemesanans.tanggal_bayar')
            ->whereBetween('pemesanans.tanggal_bayar', [$startDate, $endDate])
            ->groupBy('films.id', 'films.judul', 'films.genre', 'films.poster_image')
            ->orderBy('total_revenue', 'desc')
            ->get();

        // Calculate occupancy rate for each film
        foreach ($filmPerformance as $film) {
            $totalCapacity = Jadwal::join('studios', 'jadwals.studio_id', '=', 'studios.id')
                ->where('jadwals.film_id', $film->id)
                ->whereBetween('jadwals.tanggal_tayang', [$startDate, $endDate])
                ->sum('studios.total_kursi');
            
            $film->occupancy_rate = $totalCapacity > 0 
                ? round(($film->total_tickets / $totalCapacity) * 100, 2) 
                : 0;
        }

        return view('owner.film-performance', compact('filmPerformance', 'startDate', 'endDate'));
    }

    /**
     * Export to PDF (placeholder untuk sekarang)
     */
    public function exportPDF(Request $request)
{
    $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
    $endDate = $request->input('end_date', now()->toDateString());

    // Daily breakdown
    $dailyRevenue = Pemesanan::select(
            DB::raw('DATE(tanggal_bayar) as date'),
            DB::raw('SUM(total_bayar) as revenue'),
            DB::raw('SUM(jumlah_kursi) as tickets'),
            DB::raw('COUNT(*) as transactions'),
            DB::raw('SUM(CASE WHEN tipe_pemesanan = "online" THEN total_bayar ELSE 0 END) as online_revenue'),
            DB::raw('SUM(CASE WHEN tipe_pemesanan = "offline" THEN total_bayar ELSE 0 END) as offline_revenue')
        )
        ->where('status_pembayaran', 'sudah_bayar')
        ->whereNotNull('tanggal_bayar')
        ->whereBetween('tanggal_bayar', [$startDate, $endDate])
        ->groupBy('date')
        ->orderBy('date', 'asc') // ASC untuk PDF (kronologis)
        ->get();

    // Summary totals
    $summary = [
        'total_revenue' => $dailyRevenue->sum('revenue'),
        'total_tickets' => $dailyRevenue->sum('tickets'),
        'total_transactions' => $dailyRevenue->sum('transactions'),
        'online_revenue' => $dailyRevenue->sum('online_revenue'),
        'offline_revenue' => $dailyRevenue->sum('offline_revenue'),
        'avg_transaction' => $dailyRevenue->sum('transactions') > 0 
            ? $dailyRevenue->sum('revenue') / $dailyRevenue->sum('transactions') 
            : 0,
    ];

    // Generate PDF
    $pdf = Pdf::loadView('owner.pdf.revenue-report', compact('dailyRevenue', 'summary', 'startDate', 'endDate'))
        ->setPaper('a4', 'portrait')
        ->setOption('margin-top', 10)
        ->setOption('margin-bottom', 10)
        ->setOption('margin-left', 10)
        ->setOption('margin-right', 10);
    
    $filename = 'revenue-report-' . $startDate . '-to-' . $endDate . '.pdf';
    
    return $pdf->download($filename);
}
}
