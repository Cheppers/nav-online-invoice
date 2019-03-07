<?php

namespace NavOnlineInvoice;

/**
 * Interface ConfigInterface
 *
 * @package NavOnlineInvoice
 */
interface ConfigInterface
{
    /**
     * NAV online számla API eléréséhez használt URL
     *
     * Teszt: https://api-test.onlineszamla.nav.gov.hu/invoiceService
     * Éles: https://api.onlineszamla.nav.gov.hu/invoiceService
     *
     * @param string $baseUrl NAV eléréséhez használt környezet
     */
    public function setBaseUrl($baseUrl);

    /**
     * NAV szerverrel való kommunikáció előtt ellenőrizze az XML adatot az API sémával szemben
     *
     * @param  boolean $flag
     */
    public function useApiSchemaValidation($flag = true);

    /**
     * NAV szerverrel való kommunikáció előtt ellenőrizze az XML adatot az Data sémával szemben
     *
     * @param  boolean $flag
     */
    public function useDataSchemaValidation($flag = true);


    /**
     * @param array $data
     */
    public function setSoftware($data);
    /**
     * @param  string $jsonFile JSON file name
     * @throws \Exception
     */
    public function loadSoftware($jsonFile);

    /**
     * @param array $data
     */
    public function setUser($data);


    /**
     * @param string $jsonFile JSON file name
     * @throws \Exception
     */
    public function loadUser($jsonFile);

    /**
     * cURL hívásnál timeout beállítása másodpercekben.
     * null vagy 0 esetén nincs explicit timeout beállítás
     *
     * @param null|int $timeoutSeconds
     */
    public function setCurlTimeout($timeoutSeconds);

    /**
     * Set the additional data variable.
     *
     * @param array $data
     */
    public function setAdditionalData(array $data);

    /**
     * Returns the additional data.
     *
     * @return array
     */
    public function getAdditionalData();

    /**
     * API verzió beállítása.
     *
     * @param string $version
     */
    public function setVersion($version);

    /**
     * API verzió mappa visszaadása.
     *
     * @return string
     */
    public function getVersionDir();

    public function getDataXsdFilename();

    public function getApiXsdFilename();
}
