<?php

namespace NavOnlineInvoice\Api11;


class QueryInvoiceStatusRequestXml extends BaseRequestXml {

    function __construct($config, $transactionId, $returnOriginalRequest = false) {
        parent::__construct("QueryInvoiceStatusRequest", $config);
        $this->xml->addChild("transactionId", $transactionId);

        if ($returnOriginalRequest) {
            $this->xml->addChild("returnOriginalRequest", $returnOriginalRequest);
        }
    }

}
