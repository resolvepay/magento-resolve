<?php
/**
 * OnePica
 * NOTICE OF LICENSE
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to codemaster@onepica.com so we can send you a copy immediately.
 *
 * @category    Resolve
 * @package     Resolve_Resolve
 * @copyright   Copyright (c) 2014 One Pica, Inc. (http://www.onepica.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Class Resolve_Resolve_Model_Credential_Production
 */
class Resolve_Resolve_Model_Credential_Production extends Resolve_Resolve_Model_Credential_Abstract
{
    /**
     * Payment resolve api url
     */
    const PAYMENT_RESOLVE_API_URL = 'http://api.resolvepay.com';

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
