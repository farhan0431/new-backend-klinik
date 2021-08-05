<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\User;

class Slider extends Model
{

    protected $table = 'slider';
    // protected $appends = [
    //     // 'jadwal',
    //     // 'status_teks',
    //     // 'jenis_kecelakaan_teks',
    //     // 'gambar_link'
    // ];
    // protected $with = [
    //     'data_user'
    // ];

    // protected $appends = ['user_data'];
    protected $fillable = ['img','id_berita'];



}
