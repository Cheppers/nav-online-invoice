<?php

namespace NavOnlineInvoice;

class Config
{
    public static function factory($apiVersion, $isLive, $user, $software = null)
    {
        if ($apiVersion === '3.0') {
            return new Api30\Config($apiVersion, $isLive, $user, $software);
        }

        if ($apiVersion === '2.0') {
            return new Api20\Config($apiVersion, $isLive, $user, $software);
        }

        $software = null;
        return new Api11\Config($apiVersion, $isLive, $user, $software);
    }
}
