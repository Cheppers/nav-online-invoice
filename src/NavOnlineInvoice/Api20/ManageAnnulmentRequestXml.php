<?php

namespace NavOnlineInvoice\Api20;

class ManageAnnulmentRequestXml extends BaseRequestXml
{
    protected $transactionId;
    protected $token;
    
    function __construct($config, $transactionId, $token) {
        $this->transactionId = $transactionId;
        $this->token = $token;

        parent::__construct("ManageAnnulmentRequest", $config);
    }

    protected function createXml() {
        parent::createXml();
        $this->addToken();
        $this->addAnnulmentOperations();
    }

    protected function addToken() {
        $this->xml->addChild("exchangeToken", $this->token);
    }

    protected function addAnnulmentOperations()
    {
        $annulmentOperations = $this->xml->addChild("annulmentOperations");
        $annulmentOperation = $annulmentOperations->addChild('annulmentOperation');
        $annulmentOperation->addChild("index", '1');
        $annulmentOperation->addChild("annulmentOperation", 'ANNUL');
        $annulmentOperation->addChild("invoiceAnnulment", '');
    }
}
