<?php

namespace NavOnlineInvoice\Api30;

use NavOnlineInvoice\Traits\AddQueryData;

class QueryInvoiceDigestRequestXml extends BaseRequestXml
{
    use AddQueryData;

    public function __construct($config, $invoiceDirection, $queryData, $page = 1)
    {
        parent::__construct('QueryInvoiceDigestRequest', $config);
        $this->xml->addChild('page', $page);
        $this->xml->addChild('invoiceDirection', $invoiceDirection);
        $this->addQueryData($this->xml, 'invoiceQueryParams', $queryData);
    }
}
