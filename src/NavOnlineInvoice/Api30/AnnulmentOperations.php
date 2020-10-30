<?php

namespace NavOnlineInvoice\Api30;

use NavOnlineInvoice\Xsd;

class AnnulmentOperations
{
    const MAX_INVOICE_COUNT = 100;

    protected $annulments;
    protected $index;
    protected $config;

    function __construct(\NavOnlineInvoice\Abstracts\Config $config)
    {
        $this->annulments = [];
        $this->index = 1;
        $this->config = $config;
    }

    public function add(AnnulmentData $annulmentData, $operation = "ANNUL")
    {
        $xml = $this->generateAnnulmentXml($annulmentData);
        
        if ($this->config->validateDataSchema) {
            Xsd::validate($xml, $this->config->getAnnulmentXsdFilename());
        }

        if (count($this->annulments) > self::MAX_INVOICE_COUNT) {
            throw new Exception("Maximum " . self::MAX_INVOICE_COUNT . " számlát lehet egyszerre annulálni!");
        }

        $idx = $this->index;
        $this->index++;

        $this->annulments[] = [
            "index" => $idx,
            "operation" => $operation,
            "xml" => base64_encode($xml)
        ];

        return $idx;
    }

    public function getAnnulments()
    {
        return $this->annulments;
    }

    private function generateAnnulmentXml(AnnulmentData $annulmentData)
    {
        $annulmentXmlString = '<?xml version="1.0" encoding="UTF-8"?><InvoiceAnnulment xmlns="http://schemas.nav.gov.hu/OSA/3.0/annul"></InvoiceAnnulment>';
        $annulmentXml = new \SimpleXMLElement($annulmentXmlString);
        $annulmentXml->addChild('annulmentReference', $annulmentData->annulmentReference);
        $annulmentXml->addChild('annulmentTimestamp', $annulmentData->annulmentTimestamp);
        $annulmentXml->addChild('annulmentCode', $annulmentData->annulmentCode); // ERRATIC_DATA
        $annulmentXml->addChild('annulmentReason', $annulmentData->annulmentReason);

        return $annulmentXml->asXML();
    }
}
