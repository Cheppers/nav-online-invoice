<?php

namespace NavOnlineInvoice\Exceptions;

class GeneralExceptionResponse extends BaseExceptionResponse {

    public function getResult() {
        return (array)$this->xml;
    }

}
