<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('karyawans', function (Blueprint $table) {
            $table->id();
            $table->string('nik')->unique();
            $table->string('nama_karyawan');
            $table->string('profesi');
            $table->date('tanggal_masuk');
            $table->foreignId('ruangan_id')->constrained('ruangans')->onDelete('cascade');
            $table->integer('jatah_cuti')->default(12);
            $table->integer('cuti_diambil')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('karyawans');
    }
};
