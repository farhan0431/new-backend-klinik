<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\User;

class Berita extends Model
{

    protected $table = 'berita';
    protected $appends = [
        'gambar_link'
    ];
    // protected $with = [
    //     'data_user'
    // ];

    // protected $appends = ['user_data'];
    protected $fillable = ['desc','title','url'];

    public function getGambarLinkAttribute()
    {
            return url('berita/' . $this->img);
    }



}
