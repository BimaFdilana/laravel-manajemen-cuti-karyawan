<?php

namespace App\Http\Controllers;

use App\Models\Cuti;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {

        $sedangCutiCount = Cuti::where('tanggal_mulai_cuti', '<=', now())
            ->where('tanggal_akhir_cuti', '>=', now())
            ->count();
        $totalCutiCount = Cuti::count();
        $totalKaryawanCount = Karyawan::count();


        $cutiTerbaru = Cuti::with('karyawan')->latest()->limit(5)->get();


        $user = Auth::user();
        $notifikasiTerbaru = $user->notifications()->latest()->limit(5)->get();


        $cutiIds = $notifikasiTerbaru->pluck('data.cuti_id')->filter()->unique()->toArray();


        $cutisForNotifications = Cuti::with(['karyawan.ruangan'])
            ->whereIn('id', $cutiIds)
            ->get()
            ->keyBy('id');


        return view('pages.apps.dashboard-general-dashboard', compact(
            'sedangCutiCount',
            'totalCutiCount',
            'totalKaryawanCount',
            'cutiTerbaru',
            'notifikasiTerbaru',
            'cutisForNotifications'
        ));
    }
}
