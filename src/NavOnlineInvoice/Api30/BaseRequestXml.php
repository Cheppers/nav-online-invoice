<?php

namespace NavOnlineInvoice\Api30;

use NavOnlineInvoice\Abstracts\BaseRequestXml as BaseRequestXmlAbstract;
use NavOnlineInvoice\Util;

class BaseRequestXml extends BaseRequestXmlAbstract
{
    protected function getRequestSignatureHash()
    {
        $string = $this->getRequestSignatureString();
        $hash = Util::sha3dash512($string);
        return $hash;
    }

    protected function getInitialXmlString()
    {
        return '<?xml version="1.0" encoding="UTF-8"?><' . $this->rootName . ' xmlns:common="http://schemas.nav.gov.hu/NTCA/1.0/common" xmlns="http://schemas.nav.gov.hu/OSA/3.0/api"></' . $this->rootName . '>';
    }

    protected function addHeader()
    {
        $header = $this->xml->addChild("common:header", null, "http://schemas.nav.gov.hu/NTCA/1.0/common");

        $header->addChild("requestId", $this->requestId);
        $header->addChild("timestamp", $this->timestamp);
        $header->addChild("requestVersion", $this->config->apiVersion);
        $header->addChild("headerVersion", "1.0");
    }

    protected function addUser()
    {
        $user = $this->xml->addChild("common:user", null, "http://schemas.nav.gov.hu/NTCA/1.0/common");

        $passwordHash = Util::sha512($this->config->user["password"]);
        $signature = $this->getRequestSignatureHash();

        $user->addChild("login", $this->config->user["login"]);
        $user->addChild("passwordHash", $passwordHash)->addAttribute("cryptoType", "SHA-512");
        $user->addChild("taxNumber", $this->config->user["taxNumber"]);
        $user->addChild("requestSignature", $signature)->addAttribute("cryptoType", "SHA3-512");
    }
}
