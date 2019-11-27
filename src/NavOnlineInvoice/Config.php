<?php

namespace NavOnlineInvoice;

use Exception;

class Config
{
    public static function factory($baseUrl, $apiVersion, $user, $software = null)
    {
        if ($apiVersion === '2.0') {
            return new Api20\Config($baseUrl, $apiVersion, $user, $software);
        }

        return new Api11\Config($baseUrl, $apiVersion, $user, $software);
    }
}
