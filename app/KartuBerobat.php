<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\User;
use App\Dokter;
use App\Identitas;
use App\Janji;

class KartuBerobat extends Model
{

    protected $table = 'kartu_berobat';
    protected $appends = [
        // 'tipe_identitas',
        // 'status_teks',
        // 'jenis_kecelakaan_teks',
        // 'gambar_link'
        'identitas',
        // 'user_data',
        'dokter',
        'janji'
    ];
    // protected $fillable = ['id_dokter','jam'];


    public function getIdentitasAttribute()
    {
        return Identitas::where('id_pasien',$this->id_pasien)->first();
    }

    // public function getUserDataAttribute()
    // {
    //     return User::where('id',$this->id_pasien)->get();
    // }

    public function getDokterAttribute()
    {
        return Dokter::where('id',$this->id_dokter)->first();
    }

    public function getJanjiAttribute()
    {
        return Janji::where('nomor_antrian',$this->nomor_antrian)->first();
    }

    public function resep()
    {
        return $this->belongsTo(Resep::class,'id_janji','id_janji');
    }


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
