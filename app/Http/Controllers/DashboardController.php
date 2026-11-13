<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function index()
    {
        // Default values
        $statsPerArea = collect();
        $chartData    = collect();
        $totalToko    = 0;
        $totalSignage = 0;
        $sudahUpdate  = 0;
        $belumUpdate  = 0;
        $tidakDiketahui = 0; 
        
        // Variabel untuk Status Jaringan (Online/Offline)
        $signageOnline = 0;
        $signageOffline = 0;

        // 🔒 Multi-tenant: query manual (DB::table) tidak terkena global scope, filter client_id di sini
        $cid = \App\Support\Tenant::id() ?? 0;
        $ds   = fn () => DB::table('digital_signage')->where('client_id', $cid);

        try {
            // 1. Hitung Total Toko
            $totalToko = DB::table('toko')->where('client_id', $cid)->count();

            // 2. Hitung Signage, Status Konten, dan Status Online (1 Kueri Agregasi Cepat)
            $batas = now()->subMinutes(\App\Models\DigitalSignage::ONLINE_MENIT);

            $counts = $ds()->selectRaw('
                COUNT(*) as total_signage,
                SUM(CASE WHEN status_konten = "Sudah Terbaru" THEN 1 ELSE 0 END) as sudah_update,
                SUM(CASE WHEN status_konten = "Belum Terbaru" THEN 1 ELSE 0 END) as belum_update,
                SUM(CASE WHEN status_konten IS NULL OR status_konten = "" OR status_konten = "Tidak Diketahui" OR status_konten NOT IN ("Sudah Terbaru", "Belum Terbaru") THEN 1 ELSE 0 END) as tidak_diketahui,
                SUM(CASE WHEN last_ping >= ? THEN 1 ELSE 0 END) as online_count
            ', [$batas])->first();

            if ($counts) {
                $totalSignage   = (int) ($counts->total_signage ?? 0);
                $sudahUpdate    = (int) ($counts->sudah_update ?? 0);
                $belumUpdate    = (int) ($counts->belum_update ?? 0);
                $tidakDiketahui = (int) ($counts->tidak_diketahui ?? 0);
                $signageOnline  = (int) ($counts->online_count ?? 0);
                $signageOffline = max(0, $totalSignage - $signageOnline);
            }

            // 3. Query Area & Statistik Berdasarkan Area Toko
            $statsPerArea = DB::table('toko')
                ->where('toko.client_id', $cid)
                ->leftJoin('digital_signage as ds', 'toko.id', '=', 'ds.toko_id')
                ->select(
                    'toko.area',
                    DB::raw('COUNT(DISTINCT toko.id) as total_toko'),
                    DB::raw('SUM(CASE WHEN ds.status_konten = "Sudah Terbaru" THEN 1 ELSE 0 END) as sudah_update'),
                    DB::raw('SUM(CASE WHEN ds.status_konten = "Belum Terbaru" THEN 1 ELSE 0 END) as belum_update'),
                    DB::raw('SUM(CASE WHEN ds.status_konten IS NULL OR ds.status_konten NOT IN ("Sudah Terbaru", "Belum Terbaru") THEN 1 ELSE 0 END) as tidak_diketahui'),
                    DB::raw('SUM(CASE WHEN ds.tanggal_aktivasi >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 ELSE 0 END) as baru_aktivasi')
                )
                ->groupBy('toko.area')
                ->get();

            $chartData = $statsPerArea->map(fn($item) => [
                'area'  => $item->area,
                'total' => (int) $item->total_toko,
            ])->values();

        } catch (\Exception $e) {
            Log::error("Dashboard Load Error: " . $e->getMessage());
        }

        // Kirim seluruh variabel data yang telah disinkronkan ke View Blade
        return view('dashboard.index', compact(
            'statsPerArea',
            'totalToko',
            'totalSignage',
            'sudahUpdate',
            'belumUpdate',
            'tidakDiketahui', 
            'chartData',
            'signageOnline',   
            'signageOffline'   
        ));
    }
}