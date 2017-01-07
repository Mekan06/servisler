<?php
/**
 * Created by PhpStorm.
 * User: asus
 * Date: 05.01.2017
 * Time: 15:23
 */

namespace App;

use Illuminate\Database\Eloquent\Model;

class Iletisim extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'ad', 'soyad', 'mesaj',
    ];
    protected $hidden = [];
}