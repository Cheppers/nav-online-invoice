<?php

namespace NavOnlineInvoice\Api11;

use NavOnlineInvoice\Abstracts\Reporter as ReporterAbstract;
use NavOnlineInvoice\InvoiceOperations;

class Reporter extends ReporterAbstract
{
    /**
     * queryInvoiceStatus operáció (1.9.3 fejezet)
     *
     * A /queryInvoiceStatus a számla adatszolgáltatás feldolgozás aktuális állapotának és eredményének
     * lekérdezésére szolgáló operáció.
     *
     * @param  string  $transactionId
     * @param  boolean $returnOriginalRequest
     * @return \SimpleXMLElement  $responseXml    A teljes visszakapott XML, melyből a 'processingResults' elem releváns
     */
    public function queryInvoiceStatus($transactionId, $returnOriginalRequest = false) {
        $requestXml = new QueryInvoiceStatusRequestXml($this->config, $transactionId, $returnOriginalRequest);
        $responseXml = $this->connector->post("/queryInvoiceStatus", $requestXml);

        return $responseXml;
    }

    public function manageInvoice($invoiceOperationsOrXml, $operation = "CREATE") {

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

        return (string)$responseXml->transactionId;
    }
    /**
     * queryInvoiceData operáció (1.9.2 fejezet)
     *
     * A /queryInvoiceData a számla adatszolgáltatások lekérdezésére szolgáló operáció. A lekérdezés
     * történhet konkrét számla sorszámra, vagy lekérdezési paraméterek alapján.
     *
     * @param  string            $queryType     A queryType értéke lehet 'invoiceQuery' vagy 'queryParams'
     *                                          függően attól, hogy konktér számla sorszámot, vagy általános
     *                                          lekérdezési paramétereket adunk át.
     * @param  array             $queryData     A queryType-nak megfelelően összeállított lekérdezési adatok
     * @param  Int               $page          Oldalszám (1-től kezdve a számozást)
     * @return \SimpleXMLElement  $queryResultsXml A válasz XML queryResults része
     */
    public function queryInvoiceData($queryType, $queryData, $page = 1) {
        $requestXml = new QueryInvoiceDataRequestXml($this->config, $queryType, $queryData, $page);
        $responseXml = $this->connector->post("/queryInvoiceData", $requestXml);

        return $responseXml->queryResults;
    }
}
