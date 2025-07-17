<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cuti extends Model
{
    use HasFactory;

    protected $fillable = [
        'karyawan_id',
        'tanggal_mulai_cuti',
        'tanggal_akhir_cuti',
        'jumlah_cuti',
        'keperluan_cuti',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_mulai_cuti' => 'date',
        'tanggal_akhir_cuti' => 'date',
    ];

    protected $appends = ['progres_persentase'];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }

    public function getProgresPersentaseAttribute()
    {
        $now = Carbon::now();
        $start = $this->tanggal_mulai_cuti;
        $end = $this->tanggal_akhir_cuti->endOfDay();

        if ($now->isBefore($start)) {
            return 0;
        }

        if ($now->isAfter($end)) {
            return 100;
        }

        $totalDuration = $start->diffInDays($end) + 1;

        if ($totalDuration <= 0) {
            return 100;
        }

        $daysPassed = $start->diffInDays($now) + 1;

        $percentage = round(($daysPassed / $totalDuration) * 100);

        return min($percentage, 100);
    }
}
