<?php


class BaseTest extends PHPUnit_Framework_TestCase {

    private $config;

    public function getConfig() {
        if (!$this->config) {
            $this->config = $this->createConfig();
        }

        return $this->config;
    }


    private function createConfig() {
        return \NavOnlineInvoice\Config::factory(
            '1.0',
            false,
            TEST_DATA_DIR . "userData.sample.json",
            TEST_DATA_DIR . "softwareData.json"
        );
    }

}
