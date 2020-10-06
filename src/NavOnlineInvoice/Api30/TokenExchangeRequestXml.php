<?php

namespace NavOnlineInvoice\Api30;

class TokenExchangeRequestXml extends BaseRequestXml
{
    function __construct($config)
    {
        parent::__construct("TokenExchangeRequest", $config);
    }
}