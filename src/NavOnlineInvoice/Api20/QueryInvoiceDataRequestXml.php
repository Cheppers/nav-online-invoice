<?php

namespace NavOnlineInvoice\Api20;

class QueryInvoiceDataRequestXml extends BaseRequestXml {

    /**
     * QueryInvoiceDataRequestXml constructor.
     * @param $config
     * @param $queryData
     */
    function __construct($config, $queryData) {
        parent::__construct("QueryInvoiceDataRequest", $config);

        $this->addQueryData($this->xml, $queryType, $queryData);
    }


    protected function addQueryData(\SimpleXMLElement $xmlNode, $type, $data) {
        $node = $xmlNode->addChild($type);

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $this->addQueryData($node, $key, $value);
            } else {
                $node->addChild($key, $value);
            }
        }
    }
}
