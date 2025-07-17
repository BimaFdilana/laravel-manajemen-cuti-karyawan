<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\Ruangan;
use App\Models\User;
use App\Notifications\GeneralNotification;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Notification;

// Tambahkan use statement ini
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\KaryawansImport;
use Maatwebsite\Excel\Validators\ValidationException;

class KaryawanController extends Controller
{
    public function index(Request $request)
    {
        $query = Karyawan::with('ruangan');

        if ($request->filled('nama')) {
            $query->where('nama_karyawan', 'like', '%' . $request->nama . '%');
        }

        if ($request->filled('nik')) {
            $query->where('nik', 'like', '%' . $request->nik . '%');
        }

        $karyawans = $query->latest()->paginate(10);
        return view('pages.apps.admin.karyawan.index', compact('karyawans'));
    }

    public function create()
    {
        $ruangans = Ruangan::all();
        return view('pages.apps.admin.karyawan.create', compact('ruangans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nik' => 'required|string|max:255|unique:karyawans,nik',
            'nama_karyawan' => 'required|string|max:255',
            'profesi' => 'required|string|max:255',
            'tanggal_masuk' => 'required|date',
            'ruangan_id' => 'required|exists:ruangans,id',
            'jatah_cuti' => 'required|integer|min:0',
        ]);

        $karyawan = Karyawan::create($request->all());

        $admins = User::whereHas('role', fn($q) => $q->where('name', 'admin'))->get();
        $title = 'Karyawan Baru Ditambahkan';
        $message = "Karyawan an. {$karyawan->nama_karyawan} telah ditambahkan ke sistem.";
        $link = route('karyawan.show', $karyawan->id);
        $icon = 'fas fa-user-plus';

        Notification::send($admins, new GeneralNotification($title, $message, $link, $icon));

        return redirect()->route('karyawan.index')->with('success', 'Data Karyawan berhasil ditambahkan.');
    }

    public function show(Karyawan $karyawan)
    {
        return view('pages.apps.admin.karyawan.show', compact('karyawan'));
    }

    public function edit(Karyawan $karyawan)
    {
        $ruangans = Ruangan::all();
        return view('pages.apps.admin.karyawan.edit', compact('karyawan', 'ruangans'));
    }

    public function update(Request $request, Karyawan $karyawan)
    {
        $request->validate([
            'nik' => ['required', 'string', 'max:255', Rule::unique('karyawans')->ignore($karyawan->id)],
            'nama_karyawan' => 'required|string|max:255',
            'profesi' => 'required|string|max:255',
            'tanggal_masuk' => 'required|date',
            'ruangan_id' => 'required|exists:ruangans,id',
            'jatah_cuti' => 'required|integer|min:0',
        ]);

        $data = $request->except('cuti_diambil');
        $karyawan->update($data);

        $admins = User::whereHas('role', fn($q) => $q->where('name', 'admin'))->get();
        $title = 'Data Karyawan Diperbarui';
        $message = "Data untuk karyawan an. {$karyawan->nama_karyawan} telah diperbarui.";
        $link = route('karyawan.show', $karyawan->id);
        $icon = 'fas fa-user-edit';

        Notification::send($admins, new GeneralNotification($title, $message, $link, $icon));

        return redirect()->route('karyawan.index')->with('success', 'Data Karyawan berhasil diperbarui.');
    }

    public function destroy(Karyawan $karyawan)
    {
        $namaKaryawan = $karyawan->nama_karyawan;
        $admins = User::whereHas('role', fn($q) => $q->where('name', 'admin'))->get();
        $title = 'Karyawan Dihapus';
        $message = "Karyawan an. {$namaKaryawan} telah dihapus dari sistem.";
        $link = route('karyawan.index');
        $icon = 'fas fa-user-minus';
        Notification::send($admins, new GeneralNotification($title, $message, $link, $icon));
        $karyawan->delete();
        return redirect()->route('karyawan.index')->with('success', 'Data Karyawan berhasil dihapus.');
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        try {
            Excel::import(new KaryawansImport(), $request->file('file'));

            $admins = User::whereHas('role', fn($q) => $q->where('name', 'admin'))->get();
            $title = 'Import Karyawan Berhasil';
            $message = 'Data karyawan baru telah berhasil diimpor dari file Excel.';
            $link = route('karyawan.index');
            $icon = 'fas fa-file-excel';
            Notification::send($admins, new GeneralNotification($title, $message, $link, $icon));

            return redirect()->route('karyawan.index')->with('success', 'Data karyawan berhasil diimpor!');
        } catch (ValidationException $e) {
            $failures = $e->failures();
            $errorMessages = [];
            foreach ($failures as $failure) {
                $errorMessages[] = 'Baris ' . $failure->row() . ': ' . implode(', ', $failure->errors());
            }
            return redirect()->route('karyawan.index')->with('import_errors', $errorMessages);
        } catch (\Exception $e) {
            return redirect()
                ->route('karyawan.index')
                ->with('error', 'Terjadi kesalahan saat mengimpor data: ' . $e->getMessage());
        }
    }

    public function downloadTemplate()
    {
        $filePath = public_path('templates/template_karyawan.xlsx');
        $headers = [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ];

        if (!file_exists($filePath)) {
            if (!is_dir(public_path('templates'))) {
                mkdir(public_path('templates'), 0755, true);
            }
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setCellValue('A1', 'nik');
            $sheet->setCellValue('B1', 'nama_karyawan');
            $sheet->setCellValue('C1', 'profesi');
            $sheet->setCellValue('D1', 'tanggal_masuk');
            $sheet->setCellValue('E1', 'ruangan');
            $sheet->setCellValue('F1', 'jatah_cuti');

            $sheet->getComment('D1')->getText()->createTextRun('Format: YYYY-MM-DD atau format tanggal standar Excel.');
            $sheet->getComment('E1')->getText()->createTextRun('Isi dengan NAMA RUANGAN yang sudah ada di sistem (e.g., "Ruang Melati").');

            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->save($filePath);
        }

        return response()->download($filePath, 'template_karyawan.xlsx', $headers);
    }
}
