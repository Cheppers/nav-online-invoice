<?php

namespace NavOnlineInvoice\Api30;

use NavOnlineInvoice\Abstracts\Reporter as ReporterAbstract;
use NavOnlineInvoice\InvoiceOperations;

class Reporter extends ReporterAbstract
{
    public function tokenExchange()
    {
        $requestXml = new TokenExchangeRequestXml($this->config);
        $responseXml = $this->connector->post("/tokenExchange", $requestXml);

        $encodedToken = $responseXml->getElementsByTagName('encodedExchangeToken')->item(0);
        $encodedToken = $encodedToken ? $encodedToken->nodeValue : null;
        $token = $this->decodeToken($encodedToken);

        return $token;
    }

    public function queryTransactionStatus($transactionId, $returnOriginalRequest = false)
    {
        $requestXml = new QueryTransactionStatusRequestXml($this->config, $transactionId, $returnOriginalRequest);
        $responseXml = $this->connector->post("/queryTransactionStatus", $requestXml);

        return $responseXml;
    }

    public function manageInvoice($invoiceOperationsOrXml, $operation = "CREATE")
    {
        // Ha nem InvoiceOperations példányt adtak át, akkor azzá konvertáljuk
        if ($invoiceOperationsOrXml instanceof InvoiceOperations) {
            $invoiceOperations = $invoiceOperationsOrXml;
        } else {
            $invoiceOperations = new InvoiceOperations($this->config);

            $invoiceOperations->add($invoiceOperationsOrXml, $operation);
        }

        if (empty($this->token)) {
            $this->token = $this->tokenExchange();
        }

        $requestXml = new ManageInvoiceRequestXml($this->config, $invoiceOperations, $this->token);
        $responseXml = $this->connector->post("/manageInvoice", $requestXml);

        $transactionId = $responseXml->getElementsByTagName('transactionId')->item(0);
        return $transactionId ? $transactionId->nodeValue : null;
    }

    public function manageAnnulment($annulmentOperations, &$requestXmlString = '')
    {
        if (empty($this->token)) {
            $this->token = $this->tokenExchange();
        }

        $requestXml = new ManageAnnulmentRequestXml($this->config, $annulmentOperations, $this->token);
        $responseXml = $this->connector->post("/manageAnnulment", $requestXml);

        $requestXmlString = $requestXml->asXML();

        $transactionId = $responseXml->getElementsByTagName('transactionId')->item(0);
        return $transactionId ? $transactionId->nodeValue : null;
    }
}