<?php

namespace NavOnlineInvoice\Exceptions;

class GeneralErrorResponse extends BaseExceptionResponse
{
    public function getResult()
    {
        return $this->getXmlResult($this->xml);
    }

    private function getXmlResult(\DOMDocument $xml)
    {
        $result = $xml->getElementsByTagName('result')->item(0);
        if ($result) {
            $results = [];
            for ($i = 0; $i < $result->childNodes->length; $i++) {
                $results[$result->childNodes->item($i)->localName] = $result->childNodes->item($i)->nodeValue;
            }
            return $results;
        }
        return null;
    }
}
