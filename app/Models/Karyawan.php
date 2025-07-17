<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Karyawan extends Model
{
    use HasFactory;

    protected $fillable = [
        'nik',
        'nama_karyawan',
        'profesi',
        'tanggal_masuk',
        'ruangan_id',
        'jatah_cuti',
        'cuti_diambil',
    ];

    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class);
    }

    public function cutis()
    {
        return $this->hasMany(Cuti::class);
    }

    public function getSisaCutiAttribute()
    {
        return $this->jatah_cuti - $this->cuti_diambil;
    }
}
