<?php

namespace NavOnlineInvoice;

class Config
{
    public static function factory($apiVersion, $isLive, $user, $software = null)
    {
        if ($apiVersion === '2.0') {
            return new Api20\Config($apiVersion, $isLive, $user, $software);
        }

        return new Api11\Config($apiVersion, $isLive, $user, $software);
    }
}
