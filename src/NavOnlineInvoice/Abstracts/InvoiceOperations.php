<?php

namespace NavOnlineInvoice\Abstracts;

use NavOnlineInvoice\Abstracts\Config;

abstract class InvoiceOperations
{
    const MAX_INVOICE_COUNT = 100;

    protected $invoices;

    protected $index;

    /**
     * @var Config
     */
    protected $config;

    /**
     * Számlákat (számla műveleteket) összefogó objektum (collection) készítése
     */
    public function __construct(\NavOnlineInvoice\Abstracts\Config $config)
    {
        $this->invoices = array();
        $this->index = 1;
        $this->config = $config;
    }

    abstract public function add(string $xml, $operation = "CREATE");

    public function getInvoices()
    {
        return $this->invoices;
    }

    /**
     * XML objektum konvertálása base64-es szöveggé
     * @param string $xml
     * @return string
     */
    protected function convertXml(string $xml)
    {
        return base64_encode($xml);
    }
}