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
 * Class Resolve_Resolve_Helper_Promo_Data
 */
class Resolve_Resolve_Helper_Promo_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Config
     *
     * @var array
     */
    protected $_config;

    /**
     * Is promo active
     */
    const RESOLVE_PROMO_ACTIVE = 'resolvepromo/settings/active';

    /**
     * Is checkout button active
     */
    const PAYMENT_RESOLVE_CHECKOUT_BUTTON_ACTIVE = 'payment/resolve/checkout_button_active';

    /**
     * Checkout button image code
     */
    const PAYMENT_RESOLVE_CHECKOUT_BUTTON_CODE = 'payment/resolve/checkout_button_code';

    /**
     * Catalog product path
     */
    const RESOLVE_PROMO_CATALOG_PRODUCT_PATH = 'resolvepromo/developer_settings/path_catalog_product';

    /**
     * Catalog category path
     */
    const RESOLVE_PROMO_CATALOG_CATEGORY_PATH = 'resolvepromo/developer_settings/path_catalog_category';

    /**
     * Homepage path
     */
    const RESOLVE_PROMO_HOMEPAGE_PATH = 'resolvepromo/developer_settings/path_homepage';

    /**
     * Checkout cart path
     */
    const RESOLVE_PROMO_CHECKOUT_CART_PATH = 'resolvepromo/developer_settings/path_checkout_cart';

    /**
     * Resolve promo dev settings containers
     */
    const RESOLVE_PROMO_DEV_SETTINGS_CONTAINER = 'resolvepromo/developer_settings/container_';

    /**
     * PDP handle
     */
    const PDP_HANDLE = 'catalogproductview';

    /**
     * Returns is promo active
     *
     * @param null|Mage_Core_Model_Store $store
     * @return bool
     */
    public function isPromoActive($store = null)
    {
        return !Mage::helper('resolve')->isDisableModuleFunctionality() &&
            Mage::getStoreConfigFlag(self::RESOLVE_PROMO_ACTIVE, $store) &&
            !Mage::registry('resolve_disabled_backordered');
    }

    /**
     * Returns is checkout button active
     *
     * @param null|Mage_Core_Model_Store $store
     * @return bool
     */
    public function isCheckoutButtonActive($store = null)
    {
        return Mage::getStoreConfigFlag(self::PAYMENT_RESOLVE_CHECKOUT_BUTTON_ACTIVE, $store);
    }

    /**
     * Get checkout button code
     *
     * @param null|Mage_Core_Model_Store $store
     * @return string
     */
    public function getCheckoutButtonCode($store = null)
    {
        return Mage::getStoreConfig(self::PAYMENT_RESOLVE_CHECKOUT_BUTTON_CODE, $store);
    }

    /**
     * Get catalog product path
     *
     * @param null|Mage_Core_Model_Store $store
     * @return string
     */
    public function getCatalogProductPath($store = null)
    {
        return Mage::getStoreConfig(self::RESOLVE_PROMO_CATALOG_PRODUCT_PATH, $store);
    }

    /**
     * Get catalog category path
     *
     * @param null|Mage_Core_Model_Store $store
     * @return string
     */
    public function getCatalogCategoryPath($store = null)
    {
        return Mage::getStoreConfig(self::RESOLVE_PROMO_CATALOG_CATEGORY_PATH, $store);
    }

    /**
     * Get homepage path
     *
     * @param null|Mage_Core_Model_Store $store
     * @return string
     */
    public function getHomepagePath($store = null)
    {
        return Mage::getStoreConfig(self::RESOLVE_PROMO_HOMEPAGE_PATH, $store);
    }

    /**
     * Get checkout cart path
     *
     * @param null|Mage_Core_Model_Store $store
     * @return string
     */
    public function getCheckoutCartPath($store = null)
    {
        return Mage::getStoreConfig(self::RESOLVE_PROMO_CHECKOUT_CART_PATH, $store);
    }

    /**
     * Get container settings
     *
     * @param null|Mage_Core_Model_Store $store
     * @param string $pageCode
     * @return string
     */
    public function getContainerSettings($store = null, $pageCode)
    {
        return Mage::getStoreConfig(self::RESOLVE_PROMO_DEV_SETTINGS_CONTAINER . $pageCode, $store);
    }

    /**
     * Get configuration settings for current page
     *
     * @return Varien_Object
     */
    public function getSectionConfig()
    {
        if (null === $this->_config) {
            $codeMap = array(
                $this->getCatalogProductPath() => 'catalog_product',
                $this->getCatalogCategoryPath() => 'catalog_category',
                $this->getHomepagePath() => 'homepage',
                $this->getCheckoutCartPath() => 'checkout_cart'
            );
            $config = new Varien_Object();
            $module = Mage::app()->getRequest()->getModuleName();
            $controller = Mage::app()->getRequest()->getControllerName();
            $action = Mage::app()->getRequest()->getActionName();

            if (isset($codeMap[$module . '.' . $controller . '.' . $action])) {
                $pageCode = $codeMap[$module . '.' . $controller . '.' . $action];
                $size = Mage::getStoreConfig('resolvepromo/' . $pageCode . '/size');
                $position = Mage::getStoreConfig('resolvepromo/' . $pageCode . '/position');
                list($positionHorizontal, $positionVertical) = explode('-', $position);
                $display = Mage::getStoreConfig('resolvepromo/' . $pageCode . '/display');
                $config->setPageCode($pageCode)
                    ->setDisplay($display)
                    ->setSize($size)
                    ->setPositionHorizontal($positionHorizontal)
                    ->setPositionVertical($positionVertical);

                // each fetch container for a given page
                $config->setContainer($this->getContainerSettings(null, $pageCode));
            }
            $this->_config = $config;
        }

        return $this->_config;
    }

    /**
     * Get checkout promo Resolve js
     *
     * @return string
     */
    public function getCheckoutResolveJsScript()
    {
        if (!Mage::helper('resolve/promo_asLowAs')->isAsLowAsDisabledOnCheckout()) {
            return 'js/resolve/aslowas.js';
        }
        return 'js/resolve/noconf.js';
    }

    /**
     * Get pdp promo Resolve js
     *
     * @return string
     */
    public function getPDPResolveJsScript()
    {
        if (!Mage::helper('resolve/promo_asLowAs')->isAsLowAsDisabledOnPDP()) {
            return 'js/resolve/aslowas.js';
        }
        return 'js/resolve/noconf.js';
    }

    /**
     * Get plp promo Resolve js
     *
     * @return string
     */
    public function getPLPResolveJsScript()
    {
        if (!Mage::helper('resolve/promo_asLowAs')->isAsLowAsDisabledOnPLP()) {
            return 'js/resolve/aslowas.js';
        }
        return 'js/resolve/noconf.js';
    }
}