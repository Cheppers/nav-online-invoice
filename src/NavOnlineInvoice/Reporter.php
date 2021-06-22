<?php

namespace NavOnlineInvoice;

class Reporter
{
    public static function factory(\NavOnlineInvoice\Abstracts\Config $config)
    {
        return new Api30\Reporter($config);
    }
}
