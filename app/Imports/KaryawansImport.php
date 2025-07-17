<?php

namespace App\Imports;

use App\Models\Karyawan;
use App\Models\Ruangan;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Validation\Rule;

class KaryawansImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {

        $ruangan = Ruangan::where('nama_ruangan', trim($row['ruangan']))->first();
        $karyawan = new Karyawan();

        $karyawan->nik = $row['nik'];
        $karyawan->nama_karyawan = $row['nama_karyawan'];
        $karyawan->profesi = $row['profesi'];
        $karyawan->tanggal_masuk = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['tanggal_masuk'])->format('Y-m-d');
        $karyawan->ruangan_id = $ruangan ? $ruangan->id : null;
        $karyawan->jatah_cuti = $row['jatah_cuti'];
        $karyawan->cuti_diambil = 0;

        return $karyawan;
    }

    public function rules(): array
    {
        return [
            'nik' => 'required|max:255|unique:karyawans,nik',
            'nama_karyawan' => 'required|string|max:255',
            'profesi' => 'required|string|max:255',
            'tanggal_masuk' => 'required',
            'ruangan' => 'required|string|exists:ruangans,nama_ruangan',
            'jatah_cuti' => 'required|integer|min:0',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'ruangan.exists' => 'Pada baris :attribute, nama ruangan ":input" tidak valid atau tidak ditemukan di database.',
            'nik.unique' => 'Pada baris :attribute, NIK ":input" sudah terdaftar di sistem.',
            '*.required' => 'Kolom :attribute pada salah satu baris tidak boleh kosong.',
        ];
    }
}
