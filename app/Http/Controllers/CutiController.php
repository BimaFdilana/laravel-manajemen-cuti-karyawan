<?php

namespace App\Http\Controllers;

use App\Models\Cuti;
use App\Models\User;
use App\Models\Ruangan;
use App\Models\Karyawan;
use App\Notifications\GeneralNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Database\Eloquent\Builder;
use App\Exports\CutiExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CutiController extends Controller
{
    public function index(Request $request)
    {

        $query = Karyawan::query();


        $query->whereHas('cutis');



        $query->with(['ruangan', 'cutis' => function ($q) {
            $q->orderBy('tanggal_mulai_cuti', 'desc');
        }]);


        if ($request->filled('nama')) {
            $query->where('nama_karyawan', 'like', '%' . $request->nama . '%');
        }


        $tanggalMulai = $request->input('tanggal_mulai');
        $tanggalAkhir = $request->input('tanggal_akhir');

        if ($tanggalMulai || $tanggalAkhir) {

            $query->whereHas('cutis', function ($subQuery) use ($tanggalMulai, $tanggalAkhir) {
                if ($tanggalMulai && $tanggalAkhir) {
                    $subQuery->whereBetween('tanggal_mulai_cuti', [$tanggalMulai, $tanggalAkhir]);
                } elseif ($tanggalMulai) {
                    $subQuery->where('tanggal_mulai_cuti', '>=', $tanggalMulai);
                } elseif ($tanggalAkhir) {
                    $subQuery->where('tanggal_mulai_cuti', '<=', $tanggalAkhir);
                }
            });
        }


        $karyawans = $query->orderBy('nama_karyawan', 'asc')->get();


        return view('pages.apps.admin.cuti.index', compact('karyawans'));
    }

    public function create()
    {
        $karyawans = Karyawan::orderBy('nama_karyawan')->get();
        return view('pages.apps.admin.cuti.create', compact('karyawans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'karyawan_id' => 'required|exists:karyawans,id',
            'tanggal_mulai_cuti' => 'required|date',
            'tanggal_akhir_cuti' => 'required|date|after_or_equal:tanggal_mulai_cuti',
            'keperluan_cuti' => 'required|string',
            'keterangan' => 'required|string',
        ]);

        $karyawan = Karyawan::findOrFail($request->karyawan_id);
        $tglMulai = Carbon::parse($request->tanggal_mulai_cuti);
        $tglAkhir = Carbon::parse($request->tanggal_akhir_cuti);
        $jumlahCuti = $tglMulai->diffInDays($tglAkhir) + 1;

        if ($karyawan->sisa_cuti < $jumlahCuti) {
            return redirect()->back()->withErrors(['jumlah_cuti' => 'Karyawan ini tidak memiliki sisa jatah cuti: ' . $karyawan->sisa_cuti . 'hari .'])->withInput();
        }

        DB::transaction(function () use ($request, $karyawan, $jumlahCuti) {
            $cuti = Cuti::create([
                'karyawan_id' => $request->karyawan_id,
                'tanggal_mulai_cuti' => $request->tanggal_mulai_cuti,
                'tanggal_akhir_cuti' => $request->tanggal_akhir_cuti,
                'jumlah_cuti' => $jumlahCuti,
                'keperluan_cuti' => $request->keperluan_cuti,
                'keterangan' => $request->keterangan,
            ]);
            $karyawan->increment('cuti_diambil', $jumlahCuti);
            $admins = User::whereHas('role', fn($q) => $q->where('name', 'admin'))->get();

            $title1 = 'Pengajuan Cuti Baru';
            $message1 = "Cuti baru telah diajukan untuk {$cuti->karyawan->nama_karyawan}.";
            $link1 = route('cuti.index');
            $icon1 = 'fas fa-calendar-plus';
            Notification::send($admins, new GeneralNotification($title1, $message1, $link1, $icon1));

            $karyawan->refresh();
            $sisaCuti = $karyawan->sisa_cuti;
            $namaKaryawan = $karyawan->nama_karyawan;

            if ($sisaCuti == 0) {
                $title2 = 'Jatah Cuti Habis';
                $message2 = "Jatah cuti untuk {$namaKaryawan} telah habis.";
                $icon2 = 'fas fa-exclamation-circle';
                Notification::send($admins, new GeneralNotification($title2, $message2, '#', $icon2));
            } elseif ($sisaCuti <= 2) {
                $title2 = 'Jatah Cuti Segera Habis';
                $message2 = "Jatah cuti untuk {$namaKaryawan} tersisa {$sisaCuti} hari.";
                $icon2 = 'fas fa-exclamation-triangle';
                Notification::send($admins, new GeneralNotification($title2, $message2, '#', $icon2));
            }
        });


        return redirect()->route('cuti.index')->with('success', 'Data Cuti berhasil dibuat.');
    }

    public function edit(Cuti $cuti)
    {
        $karyawans = Karyawan::orderBy('nama_karyawan')->get();
        return view('pages.apps.admin.cuti.edit', compact('cuti', 'karyawans'));
    }

    public function update(Request $request, Cuti $cuti)
    {
        $request->validate([
            'karyawan_id' => 'required|exists:karyawans,id',
            'tanggal_mulai_cuti' => 'required|date',
            'tanggal_akhir_cuti' => 'required|date|after_or_equal:tanggal_mulai_cuti',
            'keperluan_cuti' => 'required|string',
            'keterangan' => 'required|string',
        ]);

        $karyawanBaru = Karyawan::findOrFail($request->karyawan_id);
        $tglMulaiBaru = Carbon::parse($request->tanggal_mulai_cuti);
        $tglAkhirBaru = Carbon::parse($request->tanggal_akhir_cuti);
        $jumlahCutiBaru = $tglMulaiBaru->diffInDays($tglAkhirBaru) + 1;

        if ($karyawanBaru->sisa_cuti < $jumlahCutiBaru) {
            return redirect()->back()->withErrors(['jumlah_cuti' => 'Karyawan ini tidak memiliki sisa jatah cuti: ' . $karyawanBaru->sisa_cuti . 'hari .'])->withInput();
        }

        DB::transaction(function () use ($request, $cuti, $karyawanBaru, $jumlahCutiBaru) {
            $karyawanLama = $cuti->karyawan;
            $jumlahCutiLama = $cuti->jumlah_cuti;
            $karyawanLama->decrement('cuti_diambil', $jumlahCutiLama);
            $cuti->update([
                'karyawan_id' => $request->karyawan_id,
                'tanggal_mulai_cuti' => $request->tanggal_mulai_cuti,
                'tanggal_akhir_cuti' => $request->tanggal_akhir_cuti,
                'jumlah_cuti' => $jumlahCutiBaru,
                'keperluan_cuti' => $request->keperluan_cuti,
                'keterangan' => $request->keterangan,
            ]);
            $karyawanBaru->increment('cuti_diambil', $jumlahCutiBaru);

            $admins = User::whereHas('role', fn($q) => $q->where('name', 'admin'))->get();
            $title = 'Data Cuti Diperbarui';
            $message = "Data cuti untuk {$cuti->karyawan->nama_karyawan} telah diperbarui.";
            $link = route('cuti.index');
            $icon = 'fas fa-calendar-check';
            Notification::send($admins, new GeneralNotification($title, $message, $link, $icon));
        });

        return redirect()->route('cuti.index')->with('success', 'Data Cuti berhasil diperbarui.');
    }

    public function showAllNotifications()
    {
        $user = Auth::user();


        $notifications = $user->notifications()->latest()->paginate(10);


        $allNotificationData = $user->notifications->pluck('data');


        $cutiIds = $allNotificationData->pluck('cuti_id')->filter()->unique()->toArray();
        $cutis = Cuti::with(['karyawan.ruangan'])
            ->whereIn('id', $cutiIds)
            ->get()

            ->keyBy('id');


        return view('pages.apps.admin.notifikasi.index', compact('notifications', 'cutis'));
    }

    public function markAsRead(Request $request, $id)
    {
        if ($request->user()) {
            $notification = $request->user()->notifications()->where('id', $id)->first();
            if ($notification) {
                $notification->markAsRead();
                return response()->json(['success' => true]);
            }
        }
        return response()->json(['success' => false], 404);
    }

    public function markAllNotificationsAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        return redirect()->back()->with('success', 'Semua notifikasi telah ditandai sebagai sudah dibaca.');
    }

    public function destroyNotification($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->delete();

        return redirect()->back()->with('success', 'Notifikasi berhasil dihapus.');
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
        DB::transaction(function () use ($cuti) {
            $karyawan = $cuti->karyawan;
            $jumlahCuti = $cuti->jumlah_cuti;
            $cuti->delete();
            $karyawan->decrement('cuti_diambil', $jumlahCuti);

            $admins = User::whereHas('role', fn($q) => $q->where('name', 'admin'))->get();
            $title = 'Data Cuti Dihapus';
            $message = "Data cuti untuk {$cuti->karyawan->nama_karyawan} telah dihapus.";
            $link = route('cuti.index');
            $icon = 'fas fa-calendar-times';
            Notification::send($admins, new GeneralNotification($title, $message, $link, $icon));
        });

        return redirect()->route('cuti.index')->with('success', 'Data Cuti berhasil dihapus.');
    }
}