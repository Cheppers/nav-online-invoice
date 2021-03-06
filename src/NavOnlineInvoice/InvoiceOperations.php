<?php

namespace NavOnlineInvoice;
use Exception;


class InvoiceOperations {

    const MAX_INVOICE_COUNT = 100;

    protected $invoices;

    /**
     * Az automatikusan felismert technicalAnnulment értéke az első hozzáadott számla alapján.
     * `null` esetén még nincs számla hozzáadva
     *
     * @var bool|null
     */
    protected $detectedTechnicalAnnulment = null;
    protected $index;

    /**
     * Config objektum.
     *
     * @var Config
     */
    protected $config;


    /**
     * Számlákat (számla műveleteket) összefogó objektum (collection) készítése
     */
    function __construct(Config $config) {
        $this->invoices = array();
        $this->index = 1;
        $this->config = $config;
    }


    /**
     * A setTechnicalAnnulment() metódus deprecated v0.6.0-ás verziótól.
     * A technicalAnnulment mező értéke mostantól automatikusan felismert a számlák
     * operation értékéből, így ezt külön nem kell beállítani.
     *
     * @deprecated
     * @param boolean $technicalAnnulment
     */
    public function setTechnicalAnnulment($technicalAnnulment = true) {
        trigger_error("A setTechnicalAnnulment() metódus deprecated v0.6.0-ás verziótól. "
            . "A technicalAnnulment mező értéke mostantól automatikusan felismert a számlák "
            . "operation értékéből, így ezt külön nem kell beállítani.", E_USER_DEPRECATED);
    }


    /**
     * Számla ('szakmai XML') hozzáadása
     *
     * @param \SimpleXMLElement $xml       Számla adatai (szakmai XML)
     * @param string            $operation Számlaművelet Enum(CREATE, MODIFY, STORNO, ANNUL)
     * @return int                      A beszúrt művelet sorszáma (index)
     * @throws \Exception
     */
    public function add(string $xml, $operation = "CREATE") {

        // XSD validálás
        if ($this->config->validateDataSchema) {
            Xsd::validate($xml, $this->config->getDataXsdFilename());
        }

        // TODO: ezt esetleg átmozgatni a Reporter vagy ManageInvoiceRequestXml osztályba?
        // Számlák maximum számának ellenőrzése
        if (count($this->invoices) > self::MAX_INVOICE_COUNT) {
            throw new Exception("Maximum " . self::MAX_INVOICE_COUNT . " számlát lehet egyszerre elküldeni!");
        }

        // Technical annulment flag beállítása és ellenőrzése
        $this->detectTechnicalAnnulment($operation);

        $idx = $this->index;
        $this->index++;

        $this->invoices[] = array(
            "index" => $idx,
            "operation" => $operation,
            "invoice" => $this->convertXml($xml)
        );

        return $idx;
    }


    /**
     * A felismert technicalAnnulment értékének lekérdezése.
     * Ha még nem adtunk hozzá számlát, akkor hibát fog dobni.
     *
     * @return bool       technicalAnnulment
     * @throws  Exception
     */
    public function getTechnicalAnnulment() {
        if (!$this->invoices) {
            throw new Exception("Még nincs számla hozzáadva, így a technicalAnnulment értéke nem megállapítható!");
        }

        return $this->detectedTechnicalAnnulment;
    }


    protected function detectTechnicalAnnulment($operation) {
        $currentFlag = ($operation === 'ANNUL');

        // Ha még nincs beállítva, akkor beállítjuk
        if (is_null($this->detectedTechnicalAnnulment)) {
            $this->detectedTechnicalAnnulment = $currentFlag;
        }

        // Ha a korábban beállított nem egyezik az aktuálissal, akkor hiba dobása (NAV nem fogadja el)
        if ($this->detectedTechnicalAnnulment !== $currentFlag) {
            throw new Exception("Az egyszerre feladott számlák nem tartalmazhatnak vegyesen ANNUL, illetve ettől eltérő operation értéket!");
        }
    }


    public function getInvoices() {
        return $this->invoices;
    }


    /**
     * XML objektum konvertálása base64-es szöveggé
     * @param string $xml
     * @return string
     */
    protected function convertXml(string $xml) {
        return base64_encode($xml);
    }

}
