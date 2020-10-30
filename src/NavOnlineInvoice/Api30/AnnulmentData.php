<?php

namespace NavOnlineInvoice\Api30;

class AnnulmentData
{
    public $annulmentReference;
    public $annulmentTimestamp;
    public $annulmentCode;
    public $annulmentReason;
    
    public function __construct(string $annulmentReference, string $annulmentCode, string $annulmentReason)
    {
        $this->annulmentReference = $annulmentReference;
        $this->annulmentTimestamp = BaseRequestXml::getTimestamp();
        $this->annulmentCode = $annulmentCode;
        $this->annulmentReason = $annulmentReason;
    }
}