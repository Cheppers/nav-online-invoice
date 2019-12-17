<?php

namespace NavOnlineInvoice\Api11;

use NavOnlineInvoice\Abstracts\Config as ConfigAbstract;

class Config extends ConfigAbstract
{
    public const LIVE_URL = 'https://api.onlineszamla.nav.gov.hu/invoiceService';
    public const TEST_URL = 'https://api-test.onlineszamla.nav.gov.hu/invoiceService';
}
