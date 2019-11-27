<?php

namespace NavOnlineInvoice\Api20;

use NavOnlineInvoice\Abstracts\BaseRequestXml as BaseRequestXmlAbstract;
use NavOnlineInvoice\Util;

class BaseRequestXml extends BaseRequestXmlAbstract
{
    /**
     * Aláírás generálása.
     *
     * manageInvoice esetén (ManageInvoiceRequestXml osztályban) ez a metódus felülírandó,
     * mert máshogy kell számolni az értéket (más értékeket is össze kell fűzni).
     *
     * Kapcsolódó fejezet: 1.5 A requestSignature számítása
     */
    protected function getRequestSignatureHash() {
        $string = $this->getRequestSignatureString();
        $hash = Util::sha3dash512($string);
        return $hash;
    }

    protected function getInitialXmlString() {
        return '<?xml version="1.0" encoding="UTF-8"?><' . $this->rootName . ' xmlns="http://schemas.nav.gov.hu/OSA/2.0/api"></' . $this->rootName . '>';
    }
}
