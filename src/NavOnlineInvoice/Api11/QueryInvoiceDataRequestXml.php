<?php

namespace NavOnlineInvoice\Api11;

use \Exception;
use NavOnlineInvoice\Traits\AddQueryData;

class QueryInvoiceDataRequestXml extends BaseRequestXml
{
    use AddQueryData;

    private static $queryTypes = array("invoiceQuery", "queryParams");

    /**
     * QueryInvoiceDataRequestXml constructor.
     * @param $config
     * @param $queryType
     * @param $queryData
     * @param $page
     * @throws \Exception
     */
    public function __construct($config, $queryType, $queryData, $page)
    {
        if (!in_array($queryType, self::$queryTypes)) {
            throw new Exception("Érvénytelen queryType: $queryType");
        }

        if (!is_int($page) or $page < 1) {
            throw new Exception("Érvénytelen oldalszám: " . $page);
        }

        parent::__construct("QueryInvoiceDataRequest", $config);

        $this->xml->addChild("page", $page);
        $this->addQueryData($this->xml, $queryType, $queryData);
    }
}
