<?php

namespace App\Http\Controllers;

use App\Models\Cuti;
use App\Models\User;
use App\Models\Ruangan;
use App\Notifications\CutiNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Database\Eloquent\Builder;
use App\Exports\CutiExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;

class CutiController extends Controller
{
    public function index(Request $request)
    {

        $query = Cuti::query();

        if ($request->filled('nama')) {
            $query->where('nama', 'like', '%' . $request->nama . '%');
        }
        $tanggalMulai = $request->input('tanggal_mulai');
        $tanggalAkhir = $request->input('tanggal_akhir');

        if ($tanggalMulai && $tanggalAkhir) {
            $query->whereBetween('tanggal_cuti', [$tanggalMulai, $tanggalAkhir]);
        } elseif ($tanggalMulai) {
            $query->where('tanggal_cuti', '>=', $tanggalMulai);
        } elseif ($tanggalAkhir) {
            $query->where('tanggal_cuti', '<=', $tanggalAkhir);
        }

        $cutis = $query->orderBy('tanggal_cuti', 'desc')->get();

        return view('pages.apps.admin.cuti.index', compact('cutis'));
    }

    public function create()
    {
        $ruangans = Ruangan::all();
        return view('pages.apps.admin.cuti.create', compact('ruangans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'ruangan' => 'required|string|max:255',
            'tanggal_cuti' => 'required|date',
            'jumlah_cuti' => 'required|integer|min:1',
            'keperluan_cuti' => 'required|string',
            'keterangan' => 'required|string',
        ]);
        $cuti = Cuti::create($request->all());

        $admins = User::whereHas('role', function (Builder $query) {
            $query->where('name', 'admin');
        })->get();
        $message = "Cuti baru telah didaftarkan untuk " . $cuti->nama;
        Notification::send($admins, new CutiNotification($cuti, $message));

        return redirect()->route('cuti.index')->with('success', 'Data Cuti berhasil dibuat.');
    }

    public function edit(Cuti $cuti)
    {
        $ruangans = Ruangan::all();
        return view('pages.apps.admin.cuti.edit', compact('cuti', 'ruangans'));
    }

    public function update(Request $request, Cuti $cuti)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'ruangan' => 'required|string|max:255',
            'tanggal_cuti' => 'required|date',
            'jumlah_cuti' => 'required|integer|min:1',
            'keperluan_cuti' => 'required|string',
            'keterangan' => 'required|string',
        ]);
        $cuti->update($request->all());
        return redirect()->route('cuti.index')->with('success', 'Data Cuti berhasil diperbarui.');
    }

    public function showAllNotifications()
    {
        $notifications = Auth::user()->notifications()->paginate(10);

        return view('pages.apps.admin.notifikasi.index', compact('notifications'));
    }

    public function markAllNotificationsAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();

        return redirect()->back()->with('success', 'Semua notifikasi telah ditandai sebagai sudah dibaca.');
    }

    public function export(Request $request)
    {
        $nama = $request->input('nama');
        $tanggalMulai = $request->input('tanggal_mulai');
        $tanggalAkhir = $request->input('tanggal_akhir');

        return Excel::download(new CutiExport($nama, $tanggalMulai, $tanggalAkhir), 'data-cuti-' . now()->format('Y-m-d') . '.xlsx');
    }

    public function destroy(Cuti $cuti)
    {
        $cuti->delete();
        return redirect()->route('cuti.index')->with('success', 'Data Cuti berhasil dihapus.');
    }
}