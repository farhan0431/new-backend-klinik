<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalDokter extends Model
{

    protected $table = 'jadwal_dokter';
    // protected $appends = [
    //     'tipe_identitas',
    //     'status_teks',
    //     'jenis_kecelakaan_teks',
    //     'gambar_link'
    // ];
    protected $fillable = ['id_dokter','jam'];



    // public function getGambarLinkAttribute()
    // {

    //     return url('uploads/' . $this->foto);
    // }

    // public function getTipeIdentitasAttribute()
    // {
    //     $jenis = [
    //         '0' => 'KTP',
    //         '1' => 'SIM',
    //         '2' => 'Tidak Ada'      
    //     ];

    //     return $jenis[$this->jenis_identitas];
    // }

    // public function getStatusTeksAttribute()
    // {

    //     $data = [
    //         '0' => 'Laporan Terkirim',
    //         '1' => 'Laporan Diterima',
    //         '2' => 'Ambulance Menuju TKP',
    //         '3' => 'Pasien Dibawa Kerumah Sakit',
    //         '4' => 'Pasien Ditangani',
    //         '5' => 'Selesai'
    //     ];

    //     return $data[$this->status];

    // }

    // public function getJenisKecelakaanTeksAttribute()
    // {

    //     $data = [
    //         '0' => 'Kecelakaan Tunggal',
    //         '1' => 'Kecelakaan Ganda',
    //     ];

    //     return $data[$this->jenis_kecelakaan];

    // }

}
