<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;

class PublicCutiController extends Controller
{
    public function index()
    {
        $karyawanOnLeave = Karyawan::with(['ruangan', 'cutis' => function ($query) {
                $query->orderBy('tanggal_mulai_cuti', 'desc');
            }])
            ->whereHas('cutis')
            ->get();

        $cutiDataForJs = $karyawanOnLeave->map(function ($karyawan) {
            $leavesDetails = $karyawan->cutis->map(function ($cuti) {
                $period = CarbonPeriod::create($cuti->tanggal_mulai_cuti, $cuti->tanggal_akhir_cuti);
                $allDates = [];
                foreach ($period as $date) {
                    $allDates[] = $date->isoFormat('dddd, D MMMM Y');
                }
                return [
                    'startDate' => $cuti->tanggal_mulai_cuti->format('d M Y'),
                    'endDate' => $cuti->tanggal_akhir_cuti->format('d M Y'),
                    'totalDays' => $cuti->jumlah_cuti,
                    'purpose' => $cuti->keperluan_cuti,
                    'notes' => $cuti->keterangan,
                    'allDates' => $allDates,
                ];
            });

            return [
                'employeeName' => $karyawan->nama_karyawan ?? 'N/A',
                'department' => $karyawan->ruangan->nama_ruangan ?? 'N/A',
                'leaveTaken' => $karyawan->cuti_diambil ?? 0,
                'totalAllowance' => ($karyawan->cuti_diambil ?? 0) + ($karyawan->sisa_cuti ?? 0),
                'leaves' => $leavesDetails,
            ];
        });

        return view('pages.apps.karyawan.jadwal-cuti', compact('cutiDataForJs'));
    }
}