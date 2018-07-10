<?php

abstract class Resolve_Resolve_Model_Credential_Abstract
{
    /**
     * Get api url
     *
     * @return string
     */
    abstract public function getApiUrl();

    /**
     * Get api key
     *
     * @return string
     */
    abstract public function getApiKey();

    /**
     * Get secret key
     *
     * @return string
     */
    abstract public function getSecretKey();
}
