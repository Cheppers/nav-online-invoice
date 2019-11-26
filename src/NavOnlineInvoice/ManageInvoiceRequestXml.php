<?php

namespace NavOnlineInvoice;


class ManageInvoiceRequestXml extends BaseRequestXml {

    protected $invoiceOperations;
    protected $token;


    /**
     * @param Config $config
     * @param InvoiceOperations $invoiceOperations
     * @param string $token
     */
    function __construct($config, $invoiceOperations, $token) {
        $this->invoiceOperations = $invoiceOperations;
        $this->token = $token;

        parent::__construct("ManageInvoiceRequest", $config);
    }


    protected function createXml() {
        parent::createXml();
        $this->addToken();
        $this->addInvoiceOperations();
    }


    protected function addToken() {
        $this->xml->addChild("exchangeToken", $this->token);
    }


    protected function addInvoiceOperations() {
        $operationsXml = $this->xml->addChild("invoiceOperations");

        if ((float)$this->config->apiVersion < 2) {
            $operationsXml->addChild("technicalAnnulment", $this->invoiceOperations->getTechnicalAnnulment());
        }

        // NOTE: the compression is currently not supported
        $operationsXml->addChild("compressedContent", false);

        // Számlák hozzáadása az XML-hez
        foreach ($this->invoiceOperations->getInvoices() as $invoice) {
            $invoiceXml = $operationsXml->addChild("invoiceOperation");

            $invoiceXml->addChild("index", $invoice["index"]);
            switch ($this->config->apiVersion) {
                default:
                case '1.0':
                case '1.1':
                    $invoiceXml->addChild("operation", $invoice["operation"]);
                    $invoiceXml->addChild("invoice", $invoice["invoice"]);
                    break;
                case '2.0':
                    $invoiceXml->addChild("invoiceOperation", $invoice["operation"]);
                    $invoiceXml->addChild("invoiceData", $invoice["invoice"]);
                    break;
            }
        }
    }


    /**
     * Aláírás hash értékének számításához string-ek összefűzése és visszaadása
     *
     * Kapcsolódó fejezet: 1.5 A requestSignature számítása
     */
    protected function getRequestSignatureString() {
        $string = parent::getRequestSignatureString();

        // A számlák CRC32 decimális értékének hozzáfűzése
        foreach ($this->invoiceOperations->getInvoices() as $invoice) {
            switch ($this->config->apiVersion) {
                default:
                case '1.0':
                case '1.1':
                    $string .= Util::crc32($invoice["invoice"]);
                    break;
                case '2.0':
                    $string .= Util::sha3dash512($invoice["operation"] . $invoice["invoice"]);
                    break;
            }
        }

        return $string;
    }

}
