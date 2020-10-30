<?php

namespace NavOnlineInvoice\Api30;

use NavOnlineInvoice\Traits\AddQueryData;

class QueryInvoiceDataRequestXml extends BaseRequestXml
{
    use AddQueryData;

    /**
     * QueryInvoiceDataRequestXml constructor.
     * @param $config
     * @param $queryData
     */
    public function __construct($config, $invoiceNumber, $invoiceDirection)
    {
        parent::__construct("QueryInvoiceDataRequest", $config);

        $this->addQueryData(
            $this->xml,
            'invoiceNumberQuery',
            [
                'invoiceNumber' => $invoiceNumber,
                'invoiceDirection' => $invoiceDirection,
            ]
        );
    }
}
