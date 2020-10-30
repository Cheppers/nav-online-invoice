<?php

namespace NavOnlineInvoice\Api20;

use NavOnlineInvoice\Abstracts\Reporter as ReporterAbstract;
use NavOnlineInvoice\InvoiceOperations;
use NavOnlineInvoice\TokenExchangeRequestXml;

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

    /**
     * queryInvoiceData operáció (1.9.2 fejezet)
     *
     * A /queryInvoiceData a számla adatszolgáltatások lekérdezésére szolgáló operáció. A lekérdezés
     * konkrét számla sorszámra alapján történhet.
     *
     * @param string $invoiceNumber Számla sorszám
     * @param string $invoiceDirection Számla iránya: INBOUND|OUTBOUND
     * @return \DOMNode  $queryResultsXml A válasz XML queryResults része
     */
    public function queryInvoiceData($invoiceNumber, $invoiceDirection)
    {
        $requestXml = new QueryInvoiceDataRequestXml($this->config, $invoiceNumber, $invoiceDirection);
        $responseXml = $this->connector->post("/queryInvoiceData", $requestXml);

        return $responseXml->getElementsByTagName('invoiceDataResult')->item(0);
    }

    /**
     * queryInvoiceDigest operáció (1.9.5 fejezet)
     *
     * A /queryInvoiceDigest a számla adatszolgáltatások lekérdezésére szolgáló operáció. A lekérdezés
     * lekérdezési paraméterek alapján történhet.
     * @param array $queryData Lekérdezési paraméterek
     * @param string $invoiceDirection Számla iránya: INBOUND|OUTBOUND
     * @param int $page A kért lap sorszáma
     * @return \SimpleXMLElement  $queryResultsXml A válasz XML queryResults része
     */
    public function queryInvoiceDigest($invoiceDirection, $queryData, $page = 1)
    {
        $requestXml = new QueryInvoiceDigestRequestXml($this->config, $invoiceDirection, $queryData, $page);
        $responseXml = $this->connector->post('/queryInvoiceDigest', $requestXml);
        return $responseXml->invoiceDigestResult;
    }

    public function queryInvoiceChainDigest($queryData, $page = 1)
    {
        $requestXml = new QueryInvoiceChainDigestRequestXml($this->config, $queryData, $page);
        $responseXml = $this->connector->post('/queryInvoiceChainDigest', $requestXml);
        return $responseXml->invoiceChainDigestResult;
    }
}
