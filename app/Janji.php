<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// use App\User;

class Janji extends Model
{

    protected $table = 'janji';
    protected $appends = [
        // 'tipe_identitas',
        // 'status_teks',
        // 'jenis_kecelakaan_teks',
        // 'gambar_link'
        'status_teks'
    ];
    protected $fillable = ['id_pasien','id_dokter','tanggal_janji','jam_janji','status','nomor_antrian','konfirmasi'];

    
    public function data_pasien()
    {
        return $this->belongsTo(User::class,'id_pasien');
    }

    public function data_identitas()
    {
        return $this->belongsTo(Identitas::class,'id_pasien');
    }

    public function data_dokter()
    {
        return $this->belongsTo(Dokter::class,'id_dokter');
    }
    public function resep()
    {
        return $this->belongsTo(Resep::class,'id','id_janji');
    }

    public function kartu_berobat()
    {
        return $this->belongsTo(KartuBerobat::class,'id','id_janji');
    }

    public function getStatusTeksAttribute()
    {
        if($this->status <= 1) {
            return 'Proses';
        }else if($this->status == 2) {
            return 'Menunggu Pembayaran';
        }else if($this->status == 3) {
            return 'Berhasil';
        }else if($this->status == 4) {
            return 'Dibatalkan';
        }
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
