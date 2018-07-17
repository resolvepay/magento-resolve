<?php

class Resolve_Resolve_Model_Credential_Production extends Resolve_Resolve_Model_Credential_Abstract
{
    /**
     * Payment resolve api url
     */
    const PAYMENT_RESOLVE_API_URL = 'https://apply.paywithresolve.com';

    /**
     * Payment resolve api key
     */
    const PAYMENT_RESOLVE_API_KEY = 'payment/resolve/api_key_production';

    /**
     * Payment resolve secret key
     */
    const PAYMENT_RESOLVE_SECRET_KEY = 'payment/resolve/secret_key_production';

    /**
     * Get api url
     *
     * @return string
     */
    public function getApiUrl()
    {
        return self::PAYMENT_RESOLVE_API_URL;
    }

    /**
     * Get api key
     *
     * @param Mage_Core_Model_Store $store
     * @return string
     */
     public function getApiKey($store = null)
     {
         return Mage::getStoreConfig(self::PAYMENT_RESOLVE_API_KEY, $store);
     }

    /**
     * Get secret key
     *
     * @param Mage_Core_Model_Store $store
     * @return string
     */
    public function getSecretKey($store = null)
    {
        return Mage::getStoreConfig(self::PAYMENT_RESOLVE_SECRET_KEY, $store);
    }
}
