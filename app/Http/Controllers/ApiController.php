<?php

namespace App\Http\Controllers;

use App\Messages;
use App\Product;
use App\User;
use Illuminate\Support\Facades\Input;
use Hash;

class ApiController extends Controller
{
    public function userPanel()
    {
        $token = Input::get('token');
        $uye = User::where('login_token', $token)->first();
        if ($uye) { //token kontrol
            $uyeEmail = User::where('login_token', $token)->first()->email;
            $urunler = Product::where('uye_email', $uyeEmail)->select()->get(); //kullanicinin tum urunlerini getirir
            return response()->json([
                'case' => '1',
                'urunler' => $urunler,
                'mesaj' => 'Islem basarili'
            ]);
        } else {
            return response()->json([
                'case' => '0',
                'mesaj' => 'Uye girisi yapilmali'
            ]);
        }
    }

    public function addProduct()
    {
        $uyeEmail = Input::get('uyeEmail');
        $urunAdi = Input::get('urunAdi');
        $aciklama = Input::get('aciklama');
        $fiyat = Input::get('fiyat');
        $stok = Input::get('stok');
        $token = Input::get('token');


        if (User::where('login_token', $token)->first()) { //token kontrol
            if ($uyeEmail == '' || $urunAdi == '' || $fiyat == '' || $stok == '') {
                return response()->json([
                    'case' => '0',
                    'mesaj' => 'degerler bos olmamali'
                ]);
            } else {
                if (Product::where('uye_email', $uyeEmail)->where('urun_adi', $urunAdi)->first()) {
                    $update = Product::where('uye_email', $uyeEmail)->where('urun_adi', $urunAdi)->update(['aciklama' => $aciklama, 'fiyat' => $fiyat, 'stok' => $stok]);
                    if ($update) {
                        return response()->json([
                            'case' => '1',
                            'mesaj' => 'urun guncellendi'
                        ]);
                    } else {
                        return response()->json([
                            'case' => '0',
                            'mesaj' => 'ayni degerler girildi'
                        ]);
                    }

                } else {
                    Product::create(['uye_email' => $uyeEmail, 'urun_adi' => $urunAdi, 'aciklama' => $aciklama, 'fiyat' => $fiyat, 'stok' => $stok]); //veritabanı kayıt
                    return response()->json([
                        'case' => '1',
                        'mesaj' => 'kayit basarili'
                    ]);
                }
            }
        } else {
            return response()->json([
                'case' => '0',
                'mesaj' => 'hatali oturum'
            ]);
        }
    }

    public function login()
    {
        $user = User::where('email', Input::get('email'))->first();
        if ($user && Hash::check(Input::get('password'), $user->password)) {
            return response()->json([
                'case' => '1',
                'mesaj' => 'islem basarili',
                'token' => md5(Input::get('email') . Input::get('password'))
            ]);
        } else
            return response()->json([
                'case' => '0',
                'mesaj' => 'hatali giris',
                'token' => ''
            ]);
    }

    public function register()
    {
        if (User::where('email', Input::get('email'))->first())
            return response()->json([
                'case' => '0',
                'mesaj' => 'uye kayitlidir',
                'token' => '',
            ]);
        else {
            $token = (md5(Input::get('email') . Input::get('password')));
            $user = new User();
            $user->name = Input::get('name');
            $user->surname = Input::get('surname');
            $user->email = Input::get('email');
            $user->password = bcrypt(Input::get('password'));
            $user->phone_number = Input::get('phone_number');
            $user->login_token = $token;
            $user->save();
            return response()->json([
                'case' => '1',
                'mesaj' => 'uye kayit basarili',
                'token' => $user->login_token
            ]);
        }

        /*$uye = Uyeler::where('email', Input::get('email')->first());
        return 'ds';
        return Uyeler::where('adi', 'asd')->first();*/
    }
}
