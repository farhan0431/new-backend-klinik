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
        if ($this->thumb_avatar) {
            return url('profile/' . $this->thumb_avatar);
        }
        return url('user/default-avatar.jpg');
    }



}
