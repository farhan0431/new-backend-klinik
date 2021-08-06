<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\User;

class Berita extends Model
{

    protected $table = 'berita';
    protected $appends = [
        'img'
    ];
    // protected $with = [
    //     'data_user'
    // ];

    // protected $appends = ['user_data'];
    protected $fillable = ['desc','title','url','file'];

    public function getImgAttribute()
    {
            return url('berita/' . $this->file);
    }



}
