<?php

namespace NavOnlineInvoice\Api20;

use NavOnlineInvoice\Abstracts\Config as ConfigAbstract;
use Exception;

class Config extends ConfigAbstract
{
    public function setVersion($version)
    {
        parent::setVersion($version);

        if (empty($this->software)) {
            throw new Exception("Api v2.0-tól kötelező megadni a szoftver-adatokat!");
        }
    }

    public function getAnnulmentXsdFilename()
    {
        return __DIR__ . '/../xsd/' . $this->getVersionDir() . '/invoiceAnnulment.xsd';
    }
}