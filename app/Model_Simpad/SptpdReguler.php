<?php

namespace App\Model_Simpad;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class SptpdReguler extends Model
{
    protected $connection = 'mysql_simpad';
    protected $table = 'sptpd_reguler';
    protected $guarded = [];

}