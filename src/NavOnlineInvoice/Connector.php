<?php

namespace NavOnlineInvoice;

use \NavOnlineInvoice\Exceptions\CurlError;
use \NavOnlineInvoice\Exceptions\GeneralErrorResponse;
use \NavOnlineInvoice\Exceptions\GeneralExceptionResponse;
use \NavOnlineInvoice\Exceptions\HttpResponseError;

class Connector implements ConnectorInterface {

    protected $config;


    /**
     *
     * @param Config  $config
     */
    function __construct($config) {
        $this->config = $config;
    }


    /**
     *
     * @param  string                   $url
     * @param  string|\SimpleXMLElement $requestXml
     * @return \SimpleXMLElement
     * @throws \NavOnlineInvoice\CurlError
     * @throws \NavOnlineInvoice\HttpResponseError
     * @throws \NavOnlineInvoice\GeneralExceptionResponse
     * @throws \NavOnlineInvoice\GeneralErrorResponse
     */
    public function post($url, $requestXml) {

        $url = $this->config->baseUrl . $url;
        $xmlString = is_string($requestXml) ? $requestXml : $requestXml->asXML();

        if ($this->config->validateApiSchema) {
            Xsd::validate($xmlString, $this->config->getApiXsdFilename());
        }

        $ch = $this->getCurlHandle($url, $xmlString);

        $result = curl_exec($ch);
        $errno = curl_errno($ch);
        $info = curl_getinfo($ch);
        $httpStatusCode = $info["http_code"];

        curl_close($ch);

        if ($errno) {
            throw new CurlError($errno);
        }

        $responseXml = $this->parseResponse($result);

        $domXml = new \DOMDocument();
        $domXml->loadXML($responseXml->asXML());

        if (!$responseXml) {
            throw new HttpResponseError($result, $httpStatusCode);
        }

        if ($domXml->documentElement->localName === "GeneralExceptionResponse") {
            throw new GeneralExceptionResponse($domXml);
        }

        if ($domXml->documentElement->localName === "GeneralErrorResponse") {
            throw new GeneralErrorResponse($domXml);
        }

        // TODO: felülvizsgálni, hogy ez minden esetben jó megoldás-e itt, illetve esetleg más típusú Exception dobása
        // Ha a result->funcCode !== OK értékkel, akkor Exception dobása
        if ($this->getFuncCode($domXml) !== "OK") {
            throw new GeneralErrorResponse($domXml);
        }

        // Fejlesztés idő alatt előfordult, hogy funcCode === OK, de a service nem megy
        $message = $this->getMessage($domXml);
        if (!empty($message) and preg_match("/endpoint is currently down/", $message)) {
            throw new GeneralErrorResponse($domXml);
        }

        return $responseXml;
    }


    private function getCurlHandle($url, $requestBody) {
        $ch = curl_init($url);

        $headers = array(
            "Content-Type: application/xml;charset=\"UTF-8\"",
            "Accept: application/xml"
        );

        if (!$this->config->verifySLL) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        }

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $requestBody);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);

        if ($this->config->curlTimeout) {
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
            curl_setopt($ch, CURLOPT_TIMEOUT, $this->config->curlTimeout);
        }

        return $ch;
    }


    private function parseResponse($result) {
        if (substr($result, 0, 5) !== "<?xml") {
            return null;
        }

        return simplexml_load_string($result);
    }

    private function getFuncCode(\DOMDocument $xml) {
        $result = $xml->getElementsByTagName('result')->item(0);
        $funcCode = $result->getElementsByTagName('funcCode')->item(0);
        if ($funcCode) {
            return $funcCode->nodeValue;
        }
        return null;
    }

    private function getMessage(\DOMDocument $xml) {
        $result = $xml->getElementsByTagName('result')->item(0);
        $message = $result->getElementsByTagName('message')->item(0);
        if ($message) {
            return $message->nodeValue;
        }
        return null;
    }

}
