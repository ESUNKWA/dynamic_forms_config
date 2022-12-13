<?php
namespace App\Http\Traits;
Trait Generics {

    public function otp(){

        $string = 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $string_shuffled = str_shuffle($string);
        $otp = substr($string_shuffled, 1, 6);

        return $otp;
    }

}
