<?php

namespace NavOnlineInvoice;

class Reporter
{
    public static function factory(Config $config)
    {
        if ($config->apiVersion === '2.0') {
            return new Api20\Reporter($config);
        }
        return new Api11\Reporter($config);
    }
}
