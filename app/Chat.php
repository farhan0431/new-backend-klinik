<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;
use App\User;
use App\Dokter;

class Chat extends Model
{

    

    protected $table = 'chat';
    protected $appends = [
        'data_pengirim',
        'data_penerima',
        'data_dokter'
    ];
    // protected $with = [
    //     'jadwal'
    // ];
    // protected $fillable = ['nama','fee','jabatan'];


    public function getDataPenerimaAttribute() {
        return User::find($this->id_penerima);
    }

    public function getDataPengirimAttribute() {
        return User::find($this->id_pengirim);
    }

    public function getDataDokterAttribute() {
        // return Dokter::where('id_user',$this->id_);
        $id = Auth::id();
        $id_dokter;
        if($this->id_penerima == $id)
        {
            $id_dokter = $this->id_pengirim;
        }else{
            $id_dokter = $this->id_penerima;
        }
        return Dokter::where('id_user',$id_dokter)->first();
    }

    // public function jadwal()
    // {

    //     return $this->belongsTo(JadwalDokter::class,'id','id_dokter');
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
