<?php

namespace App\Exports;

use App\Models\Cuti;
use Carbon\CarbonPeriod;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Maatwebsite\Excel\Concerns\Exportable;

class CutiExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    use Exportable;

    protected $nama;
    protected $tanggalMulai;
    protected $tanggalAkhir;
    private $rowNumber = 0;

    public function __construct($nama = null, $tanggalMulai = null, $tanggalAkhir = null)
    {
        $this->nama = $nama;
        $this->tanggalMulai = $tanggalMulai;
        $this->tanggalAkhir = $tanggalAkhir;
    }

    public function query()
    {
        $query = Cuti::query()->with(['karyawan', 'karyawan.ruangan']);
        if ($this->nama) {
            $query->whereHas('karyawan', function ($q) {
                $q->where('nama_karyawan', 'like', '%' . $this->nama . '%');
            });
        }

        if ($this->tanggalMulai && $this->tanggalAkhir) {
            $query->whereBetween('tanggal_mulai_cuti', [$this->tanggalMulai, $this->tanggalAkhir]);
        } elseif ($this->tanggalMulai) {
            $query->where('tanggal_mulai_cuti', '>=', $this->tanggalMulai);
        } elseif ($this->tanggalAkhir) {
            $query->where('tanggal_mulai_cuti', '<=', $this->tanggalAkhir);
        }

        return $query->orderBy('tanggal_mulai_cuti', 'asc');
    }

    public function headings(): array
    {
        return ['No', 'NIK', 'Nama Karyawan', 'Ruangan / Bagian', 'Jatah Cuti Tahunan', 'Sisa Jatah Cuti', 'Tanggal Cuti', 'Jumlah Hari', 'Keperluan Cuti', 'Keterangan'];
    }

    public function map($cuti): array
    {
        $this->rowNumber++;

        $period = CarbonPeriod::create($cuti->tanggal_mulai_cuti, $cuti->tanggal_akhir_cuti);
        $dates = [];

        if ($cuti->tanggal_mulai_cuti->isSameMonth($cuti->tanggal_akhir_cuti)) {
            foreach ($period as $date) {
                $dates[] = $date->format('d');
            }
            $tanggalCutiFormatted = implode(', ', $dates) . $cuti->tanggal_akhir_cuti->format('/m/Y');
        } else {
            foreach ($period as $date) {
                $dates[] = $date->format('d/m');
            }
            $tanggalCutiFormatted = implode(', ', $dates) . $cuti->tanggal_akhir_cuti->format('/Y');
        }

        return [$this->rowNumber, $cuti->karyawan->nik ?? 'N/A', $cuti->karyawan->nama_karyawan ?? 'N/A', $cuti->karyawan->ruangan->nama_ruangan ?? 'N/A', $cuti->karyawan->jatah_cuti ?? 0, $cuti->karyawan->sisa_cuti ?? 0, $tanggalCutiFormatted, $cuti->jumlah_cuti, $cuti->keperluan_cuti, $cuti->keterangan];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getRowDimension(1)->setRowHeight(32);
        $cellRange = 'A1:' . $sheet->getHighestColumn() . $sheet->getHighestRow();

        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => '000000']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'ded9c3'],
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ],
            'A1:' . $sheet->getHighestColumn() . $sheet->getHighestRow() => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => '000000'],
                    ],
                ],
            ],
        ];
    }
}
