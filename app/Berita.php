<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\User;

class Berita extends Model
{

    protected $table = 'berita';
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
    protected $fillable = ['desc','title','url'];



}