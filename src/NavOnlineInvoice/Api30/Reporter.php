<?php

namespace NavOnlineInvoice\Api30;

use NavOnlineInvoice\Abstracts\Reporter as ReporterAbstract;
use NavOnlineInvoice\InvoiceOperations;

class Reporter extends ReporterAbstract
{
    public function queryTransactionStatus($transactionId, $returnOriginalRequest = false)
    {
        $requestXml = new QueryTransactionStatusRequestXml($this->config, $transactionId, $returnOriginalRequest);
        $responseXml = $this->connector->post("/queryTransactionStatus", $requestXml);

        return $responseXml;
    }

    public function manageInvoice($invoiceOperationsOrXml, $operation = "CREATE")
    {

    }
}