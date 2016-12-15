<?php
/**
 * Created by PhpStorm.
 * User: asus
 * Date: 15.12.2016
 * Time: 18:30
 */

namespace App;


use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'uye_email', 'urun_adi', 'aciklama', 'fiyat', 'stok',
    ];
    protected $hidden = [];
}