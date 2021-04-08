<?php

namespace NavOnlineInvoice\Api30;

use NavOnlineInvoice\Abstracts\Reporter as ReporterAbstract;
use NavOnlineInvoice\Exceptions\TokenExchangeError;
use NavOnlineInvoice\Api30\InvoiceOperations;

class Reporter extends ReporterAbstract
{
    public function tokenExchange()
    {
        $requestXml = new TokenExchangeRequestXml($this->config);
        $responseXml = $this->connector->post("/tokenExchange", $requestXml);

        if (!$responseXml) {
            throw new TokenExchangeError('Empty NAV Response');
        }

        $encodedToken = $this->getDomValue($responseXml, 'encodedExchangeToken');

        $token = $this->decodeToken($encodedToken);

        if (empty($token)) {
            throw new TokenExchangeError('Couldn\'t decrypt token with exchangeKey');
        }

        return $token;
    }

    public function queryTransactionStatus($transactionId, $returnOriginalRequest = false)
    {
        $requestXml = new QueryTransactionStatusRequestXml($this->config, $transactionId, $returnOriginalRequest);
        $responseXml = $this->connector->post("/queryTransactionStatus", $requestXml);

        return $responseXml;
    }

    public function manageInvoice(
        $invoiceOperationsOrXml,
        $operation = "CREATE",
        $electronicInvoice = false,
        $invoiceHash = null
    ) {
        // Ha nem InvoiceOperations példányt adtak át, akkor azzá konvertáljuk
        if ($invoiceOperationsOrXml instanceof InvoiceOperations) {
            $invoiceOperations = $invoiceOperationsOrXml;
        } else {
            $invoiceOperations = new InvoiceOperations($this->config);

            $invoiceOperations->add($invoiceOperationsOrXml, $operation, $electronicInvoice, $invoiceHash);
        }

        if (empty($this->token)) {
            $this->token = $this->tokenExchange();
        }

        $requestXml = new ManageInvoiceRequestXml($this->config, $invoiceOperations, $this->token);
        $responseXml = $this->connector->post("/manageInvoice", $requestXml);

        return $this->getDomValue($responseXml, 'transactionId');
    }

    public function manageAnnulment($annulmentOperations, &$requestXmlString = '')
    {
        if (empty($this->token)) {
            $this->token = $this->tokenExchange();
        }

        $requestXml = new ManageAnnulmentRequestXml($this->config, $annulmentOperations, $this->token);
        $responseXml = $this->connector->post("/manageAnnulment", $requestXml);

        $requestXmlString = $requestXml->asXML();

        return $this->getDomValue($responseXml, 'transactionId');
    }

    public function queryInvoiceData($invoiceNumber, $invoiceDirection)
    {
        $requestXml = new QueryInvoiceDataRequestXml($this->config, $invoiceNumber, $invoiceDirection);
        $responseXml = $this->connector->post("/queryInvoiceData", $requestXml);

        return $responseXml;
    }

    public function queryInvoiceDigest($invoiceDirection, $queryData, $page = 1)
    {
        $requestXml = new QueryInvoiceDigestRequestXml($this->config, $invoiceDirection, $queryData, $page);
        $responseXml = $this->connector->post('/queryInvoiceDigest', $requestXml);

        return $responseXml;
    }

    public function queryInvoiceChainDigest($queryData, $page = 1)
    {
        $requestXml = new QueryInvoiceChainDigestRequestXml($this->config, $queryData, $page);
        $responseXml = $this->connector->post('/queryInvoiceChainDigest', $requestXml);

        return $responseXml;
    }

    private function getDomValue(\SimpleXMLElement $simpleXMLElement, string $tagName)
    {
        $domObject = $this->getDomObject($simpleXMLElement, $tagName);
        return $domObject ? $domObject->nodeValue : null;
    }

    private function getDomObject(\SimpleXMLElement $simpleXMLElement, string $tagName)
    {
        $domXml = new \DOMDocument();
        $domXml->loadXML($simpleXMLElement->asXML());
        return $domXml->getElementsByTagName($tagName)->item(0);
    }
}