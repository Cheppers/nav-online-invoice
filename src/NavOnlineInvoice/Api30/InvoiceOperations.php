<?php

namespace NavOnlineInvoice\Api30;
use Exception;
use NavOnlineInvoice\Abstracts\InvoiceOperations as InvoiceOperationsAbstract;
use NavOnlineInvoice\Xsd;

class InvoiceOperations extends InvoiceOperationsAbstract
{
    /**
     * Számla XML hozzáadása
     *
     * @param \SimpleXMLElement $xml       Számla adatai (szakmai XML)
     * @param string            $operation Számlaművelet Enum(CREATE, MODIFY, STORNO)
     * @param bool              $electronicInvoice Elektronikus számlázás engedélyezve
     * @param string            $invoiceHash Külső számla hash értéke (ha van)
     * @return int                      A beszúrt művelet sorszáma (index)
     * @throws \Exception
     */
    public function add(string $xml, $operation = "CREATE", $electronicInvoice = false, $invoiceHash = null)
    {
        // XSD validálás
        if ($this->config->validateDataSchema) {
            Xsd::validate($xml, $this->config->getDataXsdFilename());
        }

        // TODO: ezt esetleg átmozgatni a Reporter vagy ManageInvoiceRequestXml osztályba?
        // Számlák maximum számának ellenőrzése
        if (count($this->invoices) > self::MAX_INVOICE_COUNT) {
            throw new Exception("Maximum " . self::MAX_INVOICE_COUNT . " számlát lehet egyszerre elküldeni!");
        }

        $idx = $this->index;
        $this->index++;

        $this->invoices[] = [
            "index" => $idx,
            "operation" => $operation,
            "invoice" => $this->convertXml($xml),
            "electronicInvoice" => $electronicInvoice,
            "invoiceHash" => $invoiceHash,
        ];

        return $idx;
    }
}
