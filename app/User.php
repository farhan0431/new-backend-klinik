<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
// use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

use App\Roles;


// class User extends Authenticatable implements JWTSubject
class User extends Model implements AuthenticatableContract, AuthorizableContract, JWTSubject
{
    use Authenticatable, Authorizable;
    use CanResetPassword;
    // use Notifiable;

    protected $guarded = [];
    protected $hidden = ['password', 'remember_token'];
    protected $dates = ['created_at'];
    protected $appends = ['created_at_format', 'avatar_link'];

    public function getAvatarLinkAttribute()
    {
        if ($this->thumb_avatar) {
            return url('profile/' . $this->thumb_avatar);
        }
        return url('user/default-avatar.jpg');
    }

    // Rest omitted for brevity

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function getCreatedAtFormatAttribute()
    {
        return $this->created_at->format('d-m-Y');
        // return '11-02-2020';
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function tempat_pembayaran()
    {
        return $this->belongsTo('App\Model_ref\TempatBayar', 'tempat_pembayaran_id');
    }

    public function role_name()
    {
        $roles = [
            '1' => 'Super Admin',
            '2' => 'Pengguna Biasa'
        ];

        return $this->belongsTo(Roles::class,'role_id');

        // return $roles[$this->role_id];
    }
}
