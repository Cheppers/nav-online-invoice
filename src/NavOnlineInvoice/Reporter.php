<?php

namespace NavOnlineInvoice;

class Reporter
{
    public static function factory(\NavOnlineInvoice\Abstracts\Config $config)
    {
        if ($config->apiVersion === '3.0') {
            return new Api30\Reporter($config);
        }

        if ($config->apiVersion === '2.0') {
            return new Api20\Reporter($config);
        }

        return new Api11\Reporter($config);
    }
}
