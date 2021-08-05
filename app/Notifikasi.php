<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\User;
use App\Dokter;
use App\Identitas;
use App\Janji;
use App\KartuBerobat;

class Notifikasi extends Model
{

    protected $table = 'notifikasi';
    protected $appends = [
        // 'tipe_identitas',
        // 'status_teks',
        // 'jenis_kecelakaan_teks',
        // 'gambar_link'
        // 'identitas',
        // 'user_data',
        // 'dokter',
        // 'janji',
        // 'kartu_berobat'

    ];
    protected $guard = [];
    protected $fillable = ['id_user','title','status','page','desc','tambahan'];


    // public function getKartuBerobatAttribute()
    // {
    //     return Identitas::where('id',$this->id_kartu_obat)->first();
    // }

    // public function getUserDataAttribute()
    // {
    //     return User::where('id',$this->id_pasien)->get();
    // }

    // public function getDokterAttribute()
    // {
    //     return Dokter::where('id',$this->id_dokter)->first();
    // }

    // public function getJanjiAttribute()
    // {
    //     return Janji::where('id_pasien',$this->id_pasien)->first();
    // }


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
