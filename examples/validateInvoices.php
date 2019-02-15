<?php

include("config.php");

$config = new NavOnlineInvoice\Config($apiUrl, $userDataFilename);
// Az `add()` metódus hívásakor az átadott XML-ek validálva lesznek. (Hiba esetén `XsdValidationError` exception lesz dobva).

$invoices = new NavOnlineInvoice\InvoiceOperations($config);


try {

    $invoices->add(simplexml_load_file(TEST_DATA_DIR . "invoice1.xml"));
    $invoices->add(simplexml_load_file(TEST_DATA_DIR . "invoice1_invalid.xml"));

    print "Az XML-ek validak.";

} catch(Exception $ex) {
    print get_class($ex) . ": " . $ex->getMessage();
}
