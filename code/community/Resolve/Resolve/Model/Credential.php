<?php

class Resolve_Resolve_Model_Credential
{
    /**
     * Payment resolve account mode
     */
    const PAYMENT_RESOLVE_ACCOUNT_MODE = 'payment/resolve/account_mode';

    /**
     * Account mode sandbox
     */
    const ACCOUNT_MODE_SANDBOX = 'sandbox';

    /**
     * Account mode production
     */
    const ACCOUNT_MODE_PRODUCTION = 'production';
    /**
     * Info block type
     *
     * @var array
     */
    protected $_credentialModelsCache;

    /**
     * Get store id for cache
     *
     * @param Mage_Core_Model_Store|int|null $store
     * @return int
     */
    protected function _getStoreIdForCache($store = null)
    {
        $id = null;
        if ($store instanceof Mage_Core_Model_Store) {
            $id = $store->getStoreId();
        }
        if (!$id) {
            $id = $store ? $store : 0;
        }
        return $id;
    }

    /**
     * Get credential model due to current account type
     *
     * @param Mage_Core_Model_Store $store
     * @return mixed
     * @throws Resolve_RESOLVE_Exception
     */
    protected function _getCredentialModel($store = null)
    {
        $storeCacheId = $this->_getStoreIdForCache($store);
        if (!isset($this->_credentialModelsCache[$storeCacheId])) {
            $mode = Mage::getStoreConfig(self::PAYMENT_RESOLVE_ACCOUNT_MODE, $store);
            $modelClass = 'resolve/credential_' . $mode;
            $model = Mage::getModel($modelClass);
            if (!$model) {
                throw new Resolve_Resolve_Exception('Could not found model ' . $modelClass);
            }
            $this->_credentialModelsCache[$storeCacheId] = $model;
        }
        return $this->_credentialModelsCache[$storeCacheId];
    }

    /**
     * Get api url
     *
     * @param Mage_Core_Model_Store $store
     * @return string
     */
    public function getApiUrl($store = null)
    {
        return $this->_getCredentialModel($store)->getApiUrl();
    }

    /**
     * Get api key
     *
     * @param Mage_Core_Model_Store $store
     * @return string
     */
    public function getApiKey($store = null)
    {
        return $this->_getCredentialModel($store)->getApiKey($store);
    }

    /**
     * Get secret key
     *
     * @param Mage_Core_Model_Store $store
     * @return string
     */
    public function getSecretKey($store = null)
    {
        return $this->_getCredentialModel($store)->getSecretKey($store);
    }
}
