<?php

namespace App\Http\Controllers;

use App\Iletisim;
use App\Messages;
use App\Product;
use App\User;
use Illuminate\Support\Facades\Input;
use Hash;

class ApiController extends Controller
{
    public function iletisim()
    {
        $ad = Input::get('ad');
        $soyad = Input::get('soyad');
        $mesaj = Input::get('mesaj');

        $iletisim = Iletisim::create(['ad' => $ad, 'soyad' => $soyad, 'mesaj' => $mesaj]);
        if ($iletisim) {
            return response()->json([
                'case' => '1',
                'mesaj' => 'Form yollandi'
            ]);
        } else {
            return response()->json([
                'case' => '0',
                'mesaj' => 'Hata olustu-server'
            ]);
        }
    }

    public function mainPanel()
    {
        $urunler = Product::select()->get(); // tum urunleri getirir
        if ($urunler) {
            return response()->json([
                'case' => '1',
                'urunler' => $urunler,
                'mesaj' => 'Islem basarili'
            ]);
        } else {
            return response()->json([
                'case' => '0',
                'mesaj' => 'Urunler cekilirken hata olustu'
            ]);
        }
    }

    public function userPanel()
    {
        $token = Input::get('token');
        $uye = User::where('login_token', $token)->first();
        if ($uye) { //token kontrol
            $uyeEmail = User::where('login_token', $token)->first()->email;
            $urunler = Product::where('uye_email', $uyeEmail)->select()->get(); //kullanicinin tum urunlerini getirir
            if ($urunler) {
                return response()->json([
                    'case' => '1',
                    'urunler' => $urunler,
                    'mesaj' => 'Islem basarili'
                ]);
            } else {
                return response()->json([
                    'case' => '0',
                    'urunler' => $urunler,
                    'mesaj' => 'Kayitli urun yok'
                ]);
            }

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
        $sehir = Input::get('sehir');
        $token = Input::get('token');


        if (User::where('login_token', $token)->first()) { //token kontrol
            if ($uyeEmail == '' || $urunAdi == '' || $fiyat == '' || $stok == '') {
                return response()->json([
                    'case' => '0',
                    'mesaj' => 'degerler bos olmamali'
                ]);
            } else {
                if (Product::where('uye_email', $uyeEmail)->where('urun_adi', $urunAdi)->first()) {
                    $update = Product::where('uye_email', $uyeEmail)->where('urun_adi', $urunAdi)->update(['aciklama' => $aciklama, 'fiyat' => $fiyat, 'stok' => $stok, 'sehir' => $sehir]);
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
                    $create = Product::create(['uye_email' => $uyeEmail, 'urun_adi' => $urunAdi, 'aciklama' => $aciklama, 'fiyat' => $fiyat, 'stok' => $stok, 'sehir' => $sehir]); //veritabanı kayıt
                    if ($create) {
                        return response()->json([
                            'case' => '1',
                            'mesaj' => 'urun kayit basarili'
                        ]);
                    } else {
                        return response()->json([
                            'case' => '0',
                            'mesaj' => 'degerler eklenirken bir hata olustu'
                        ]);
                    }
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
