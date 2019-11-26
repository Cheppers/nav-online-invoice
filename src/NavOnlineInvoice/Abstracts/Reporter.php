<?php

namespace NavOnlineInvoice\Abstracts;

use NavOnlineInvoice\Connector;
use NavOnlineInvoice\ConnectorInterface;
use NavOnlineInvoice\Exceptions\UnsupportedMethodException;
use NavOnlineInvoice\Exceptions\XsdValidationError;
use NavOnlineInvoice\InvoiceOperations;
use NavOnlineInvoice\ManageInvoiceRequestXml;
use NavOnlineInvoice\QueryInvoiceDataRequestXml;
use NavOnlineInvoice\QueryInvoiceStatusRequestXml;
use NavOnlineInvoice\QueryTaxpayerRequestXml;
use NavOnlineInvoice\QueryTransactionStatusRequestXml;
use NavOnlineInvoice\TokenExchangeRequestXml;
use NavOnlineInvoice\Util;
use NavOnlineInvoice\Xsd;

abstract class Reporter {

    protected $connector;
    protected $config;
    protected $token;


    /**
     *
     *
     * @param Config $config    Config object (felhasználó adatok, szoftver adatok, URL, stb.)
     */
    function __construct($config) {
        $this->config = $config;
        $this->connector = new Connector($config);
    }


    /**
     * Egyedi connector osztály beállítása.
     * Alap esetben a beépített Connectort használjuk.
     * A tesztelést is megkönnyíti, hiszen így a Connector osztály mockolható.
     *
     * @param ConnectorInterface $connector
     */
    public function setConnector(ConnectorInterface $connector) {
        $this->connector = $connector;
    }


    /**
     * Egyedi connector osztály kikérése.
     * Alap esetben a beépített Connectort használjuk.
     * A tesztelést is megkönnyíti, hiszen így a Connector osztály mockolható.
     *
     * @return ConnectorInterface
     */
    public function getConnector() {
        return $this->connector;
    }


    /**
     * Token értékének beállítása.
     */
    public function setToken(string $token) {
        $this->token = $token;
    }


    /**
     * Token értékének visszaadása.
     *
     * @return string
     */
    public function getToken() {
        return $this->token;
    }


    /**
     * manageInvoice operáció (1.9.1 fejezet)
     *
     * A /manageInvoice a számla adatszolgáltatás beküldésére szolgáló operáció, ezen keresztül van
     * lehetőség számla, módosító vagy stornó számla adatszolgáltatást, illetve ezek technikai javításait a
     * NAV részére beküldeni.
     *
     * Első paraméterben át lehet adni egy InvoiceOperations példányt, mely több számlát is tartalmazhat, vagy
     * át lehet adni közvetlenül egy darab számla SimpleXMLElement példányt.
     * A második paraméter ($operation) csak és kizárólag akkor játszik szerepet, ha követlenül számla XML-lel
     * hívjuk ezt a metódust. InvoiceOperations példány esetén az operation-t ez a példány tartalmazza.
     *
     * A `technicalAnnulment` flag értéke automatikusan felismert és beállításra kerül az `operation` értékéből.
     *
     * @param  InvoiceOperations|string $invoiceOperationsOrXml
     * @param  string                             $operation
     * @return string                             $transactionId
     */
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
        if ((float)$this->config->apiVersion >= 2) {
            throw new UnsupportedMethodException('Method not supported from v2.0');
        }

        $requestXml = new QueryInvoiceStatusRequestXml($this->config, $transactionId, $returnOriginalRequest);
        $responseXml = $this->connector->post("/queryInvoiceStatus", $requestXml);

        return $responseXml;
    }

    /**
     * queryTransactionStatus operáció (API 2.0)
     *
     * A /queryTransactionStatus a számla adatszolgáltatás feldolgozás aktuális állapotának és eredményének
     * lekérdezésére szolgáló operáció.
     *
     * @param  string  $transactionId
     * @param  boolean $returnOriginalRequest
     * @return \SimpleXMLElement  $responseXml    A teljes visszakapott XML, melyből a 'processingResults' elem releváns
     */
    public function queryTransactionStatus($transactionId, $returnOriginalRequest = false) {
        if ((float)$this->config->apiVersion < 2) {
            throw new UnsupportedMethodException('Method not supported in v1.x');
        }

        $requestXml = new QueryTransactionStatusRequestXml($this->config, $transactionId, $returnOriginalRequest);
        $responseXml = $this->connector->post("/queryTransactionStatus", $requestXml);

        return $responseXml;
    }


    /**
     * queryTaxpayer operáció (1.9.4 fejezet)
     *
     * A /queryTaxpayer belföldi adószám validáló operáció, mely a számlakiállítás folyamatába építve képes
     * a megadott adószám valódiságáról és érvényességéről a NAV adatbázisa alapján adatot szolgáltatni.
     *
     * @param  string $taxNumber            Adószám, pattern: [0-9]{8}
     * @return bool|\SimpleXMLElement     Nem létező adószám esetén `null`, érvénytelen adószám esetén `false` a visszatérési érték, valid adószám estén
     *                                      pedig a válasz XML taxpayerData része (SimpleXMLElement), mely a nevet és címadatokat tartalmazza.
     */
    public function queryTaxpayer($taxNumber) {
        $requestXml = new QueryTaxpayerRequestXml($this->config, $taxNumber);
        $responseXml = $this->connector->post("/queryTaxpayer", $requestXml);

        // 1.9.4.2 fejezet alapján (QueryTaxpayerResponse) a taxpayerValidity tag csak akkor kerül a válaszba, ha a lekérdezett adószám létezik.
        // Nem létező adószámra csak egy <funcCode>OK</funcCode> kerül visszaadásra (funcCode===OK megléte a Connector-ban ellenőrizve van).
        if (!isset($responseXml->taxpayerValidity)) {
            return null;
        }

        // taxpayerValidity értéke lehet false is, ha az adószám létezik, de nem érvényes
        if (empty($responseXml->taxpayerValidity) or $responseXml->taxpayerValidity === "false") {
            return false;
        }

        // Az adószám valid, adózó adatainak visszaadása
        return $responseXml->taxpayerData;
    }


    /**
     * Token kérése manageInvoice művelethez.
     *
     * Ezt a metódust lehet használni tesztelésre is, hogy a megadott felhasználói adatok helyesek-e/a NAV szervere visszatér-e valami válasszal.
     *
     * Megjegyzés: csak a token kerül visszaadásra, az érvényességi idő nem. Ennek oka, hogy a tokent csak egy kéréshez (egyszer) lehet használni
     * NAV fórumon elhangzottak alapján (megerősítés szükséges!), és ez az egyszeri felhasználás azonnal megtörténik a token lekérése után (manageInvoice hívás).
     *
     * @return string       Token
     */
    public function tokenExchange() {
        $requestXml = new TokenExchangeRequestXml($this->config);
        $responseXml = $this->connector->post("/tokenExchange", $requestXml);

        $encodedToken = (string)$responseXml->encodedExchangeToken;
        $token = $this->decodeToken($encodedToken);

        return $token;
    }


    protected function decodeToken($encodedToken) {
        return Util::aes128_decrypt($encodedToken, $this->config->user["exchangeKey"]);
    }


    /**
     * Paraméterben átadott adat XML-t validálja az XSD-vel és hiba esetén string-ként visszaadja a hibát.
     * Ha nincs hiba, akkor visszatérési érték `null`.
     *
     * @param  \SimpleXMLElement $xml   Számla XML
     * @return null|string             Hibaüzenet, vagy `null`, ha helyes az XML
     */
    public function getInvoiceValidationError($xml) {
        try {
            Xsd::validate($xml->asXML(), $this->config->getDataXsdFilename());
        } catch (XsdValidationError $ex) {
            return $ex->getMessage();
        }
        return null;
    }

}