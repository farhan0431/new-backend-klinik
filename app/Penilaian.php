<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Janji;

class Penilaian extends Model
{

    protected $table = 'penilaian';
    protected $fillable = ['id_janji','id_dokter','penilaian','catatan'];
    protected $appends = ['janji'];


    public function getJanjiAttribute()
    {
        return Janji::with('data_pasien')->where('id',$this->id_janji)->first();
    }

}
