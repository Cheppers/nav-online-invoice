<?php

namespace NavOnlineInvoice;

class Config
{
    public static function factory($apiVersion, $isLive, $user, $software = null)
    {
        return new Api30\Config($apiVersion, $isLive, $user, $software);
    }
}
