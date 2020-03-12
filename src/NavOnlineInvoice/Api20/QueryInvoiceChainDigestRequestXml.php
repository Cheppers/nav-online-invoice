<?php

namespace NavOnlineInvoice\Api20;

use NavOnlineInvoice\Traits\AddQueryData;

class QueryInvoiceChainDigestRequestXml extends BaseRequestXml
{
    use AddQueryData;

    public function __construct($config, $queryData, $page = 1)
    {
        parent::__construct('QueryInvoiceChainDigestRequest', $config);
        $this->xml->addChild('page', $page);
        $this->addQueryData($this->xml, 'invoiceChainQuery', $queryData);
    }
}