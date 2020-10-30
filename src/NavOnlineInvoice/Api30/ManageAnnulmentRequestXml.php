<?php

namespace NavOnlineInvoice\Api30;

use NavOnlineInvoice\Util;
use NavOnlineInvoice\Xsd;

class ManageAnnulmentRequestXml extends BaseRequestXml
{
    protected $annulmentOperations;
    protected $token;

    /**
     * @param Config $config
     * @param AnnulmentOperations $annulmentOperations
     * @param string $token
     */
    function __construct($config, $annulmentOperations, $token)
    {
        $this->annulmentOperations = $annulmentOperations;
        $this->token = $token;

        parent::__construct("ManageAnnulmentRequest", $config);
    }

    protected function createXml()
    {
        parent::createXml();
        $this->addToken();
        $this->addAnnulmentOperations();
    }

    protected function addToken()
    {
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
