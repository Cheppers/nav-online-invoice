<?php

namespace NavOnlineInvoice;

class Util
{
    public static function sha512($string)
    {
        return strtoupper(hash("sha512", $string));
    }

    public static function sha3dash512($string)
    {
        return strtoupper(hash("sha3-512", $string));
    }

    public static function aes128_decrypt($string, $key)
    {
        return openssl_decrypt($string, "AES-128-ECB", $key);
    }
}
