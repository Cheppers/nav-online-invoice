<?php

namespace NavOnlineInvoice;

use Exception;

class Config
{
    public static function factory($baseUrl, $apiVersion, $user, $software = null)
    {
        if ($apiVersion === '2.0') {
            $config = new Api20\Config($baseUrl, $user, $software);
            $config->setVersion($apiVersion);
            return $config;
        }

        $config = new Api11\Config($baseUrl, $user, $software);
        $config->setVersion($apiVersion);
        return $config;
    }
}
