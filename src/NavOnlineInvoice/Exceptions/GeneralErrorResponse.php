<?php

namespace NavOnlineInvoice\Exceptions;

class GeneralErrorResponse extends BaseExceptionResponse {

    public function getResult() {
        return (array)$this->xml->result;
    }

}
