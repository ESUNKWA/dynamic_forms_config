<?php
namespace App\Http\Traits;
use Nullix\CryptoJsAes\CryptoJsAes;
/**
 * Cryptage et décryptage des données
 */
trait cryptData
{
    /**
     * cryptage des données
     */
    public function crypt($data){
        try {
            return CryptoJsAes::encrypt($data, "123456789");
        } catch (\Throwable $e) {
            return $e->getMessage();
        }

    }

    /**
     * Décryptage des données
     */
    public function decryptData($data){
        try {
            return CryptoJSAES::decrypt(json_encode($data),"123456789");
        } catch (\Throwable $e) {
            return $e->getMessage();
        }
    }
}
