<?php

namespace NavOnlineInvoice\Api20;

use NavOnlineInvoice\Util;
use NavOnlineInvoice\Xsd;

class ManageAnnulmentRequestXml extends BaseRequestXml
{
    protected $annulments;
    protected $annulmentOperations;
    protected $token;
    
    function __construct($config, $annulments, $token) {
        $this->annulments = $annulments;
        $this->token = $token;

        $this->annulmentOperations = new AnnulmentOperations($config);

        foreach ($this->annulments as $annulmentData) {
            $annulmentData['annulmentTimestamp'] = $this->getTimestamp();

            $this->annulmentOperations->add($annulmentData);
        }

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
        $annulmentXml = $this->xml->addChild("annulmentOperations");

        foreach ($this->annulmentOperations->getAnnulments() as $annulment) {
            $annulmentOperation = $annulmentXml->addChild('annulmentOperation');
            $annulmentOperation->addChild("index", $annulment["index"]);
            $annulmentOperation->addChild("annulmentOperation", $annulment["operation"]);
            $annulmentOperation->addChild("invoiceAnnulment", $annulment["xml"]);
        }
    }

    protected function getRequestSignatureString()
    {
        $string = parent::getRequestSignatureString();

        foreach ($this->annulmentOperations->getAnnulments() as $annulment) {
            $string .= Util::sha3dash512($annulment["operation"] . $annulment["xml"]);
        }

        return $string;
    }
}
