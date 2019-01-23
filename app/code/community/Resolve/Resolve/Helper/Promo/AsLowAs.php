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
 * Class Resolve_Resolve_Helper_Promo_AsLowAs
 */
class Resolve_Resolve_Helper_Promo_AsLowAs extends Mage_Core_Helper_Abstract
{
    /**
     * List of skipped product types
     *
     * @var array
     */
    protected $_skippedTypes = array(Mage_Catalog_Model_Product_Type::TYPE_GROUPED);

    /**
     * Disabled back ordered on cart
     *
     * @var bool
     */
    protected $_resolveDisabledBackOrderedCart;

    /**
     * Disabled back ordered on PDP
     *
     * @var bool
     */
    protected $_resolveDisabledBackOrderedPdp;

    /**
     * Disabled back ordered on PLP
     *
     * @var bool
     */
    protected $_resolveDisabledBackOrderedPlp;

    /**
     * Visibility as low as on PDP
     */
    const RESOLVE_PROMO_AS_LOW_AS_PDP = 'resolvepromo/as_low_as/as_low_as_pdp';

    /**
     * Visibility as low as on PLP
     */
    const RESOLVE_PROMO_AS_LOW_AS_PLP = 'resolvepromo/as_low_as/as_low_as_plp';

    /**
     * Visibility as low as on cart
     */
    const RESOLVE_PROMO_AS_LOW_AS_CART = 'resolvepromo/as_low_as/as_low_as_cart';

    /**
     * As low as APR value
     */
    const RESOLVE_PROMO_AS_LOW_AS_APR_VALUE = 'resolvepromo/as_low_as/apr_value';

    /**
     * As low as months options
     */
    const RESOLVE_PROMO_AS_LOW_AS_PROMO_MONTHS = 'resolvepromo/as_low_as/promo_months';

    /**
     * MPP min amount value
     */
    const MPP_MIN_DISPLAY_VALUE = 'resolvepromo/as_low_as/min_mpp_display_value';

    /**
     * Visibility of learn more with ala
     */
    const RESOLVE_PROMO_LEARN_MORE = 'resolvepromo/as_low_as/learn_more';

    protected $_allRules = null;

    /**
     * Check is visible on PDP
     *
     * @param null|Mage_Core_Model_Store $store
     * @return string
     */
    public function isVisibleOnPDP($store = null)
    {
        return Mage::getStoreConfig(self::RESOLVE_PROMO_AS_LOW_AS_PDP, $store);
    }

    /**
     * Check is visible on PLP
     *
     * @param null|Mage_Core_Model_Store $store
     * @return string
     */
    public function isVisibleOnPLP($store = null)
    {
        return Mage::getStoreConfig(self::RESOLVE_PROMO_AS_LOW_AS_PLP, $store);
    }

    /**
     * Check is visible on cart
     *
     * @param null|Mage_Core_Model_Store $store
     * @return string
     */
    public function isVisibleOnCart($store = null)
    {
        return Mage::getStoreConfig(self::RESOLVE_PROMO_AS_LOW_AS_CART, $store);
    }

    /**
     * Get APR value
     *
     * @param null|Mage_Core_Model_Store $store
     * @return string
     */
    public function getAPRValue($store = null)
    {
        return Mage::getStoreConfig(self::RESOLVE_PROMO_AS_LOW_AS_APR_VALUE, $store);
    }

    /**
     * Get promo months options
     *
     * @param null|Mage_Core_Model_Store $store
     * @return string
     */
    public function getPromoMonths($store = null)
    {
        return Mage::getStoreConfig(self::RESOLVE_PROMO_AS_LOW_AS_PROMO_MONTHS, $store);
    }

    /**
     * Get Minimum amount for displaying the (MPP - monthly payment pricing)
     *
     * @param Mage_Core_Model_Store $store
     * @return string
     */
    public function getMinMPP($store = null)
    {
        return Mage::getStoreConfig(self::MPP_MIN_DISPLAY_VALUE, $store);
    }

    /**
     * Skip As Low As message for specific product types
     *
     * @return bool
     */
    protected function _isSkipProductByType()
    {
        $product = Mage::helper('catalog')->getProduct();
        if ((null === $product) || !$product->getId()) {
            return true;
        }
        return in_array($product->getTypeId(), $this->_skippedTypes);
    }

    /**
     * Is As Low As disabled on checkout
     *
     * @return bool
     */
    public function isAsLowAsDisabledOnCheckout()
    {
        if (null === $this->_resolveDisabledBackOrderedCart) {
            $this->_resolveDisabledBackOrderedCart = Mage::helper('resolve')->isDisableQuoteBackOrdered() ||
                    Mage::helper('resolve')->isDisableModuleFunctionality() ||
                    !$this->isVisibleOnCart() || !Mage::helper('resolve')->isResolvePaymentMethodEnabled() ||
                    !$this->isQuoteItemsDisabledByPaymentRestRules();
        }
        return $this->_resolveDisabledBackOrderedCart;
    }

    /**
     * Is As Low As disabled on PDP
     *
     * @return bool
     */
    public function isAsLowAsDisabledOnPDP()
    {
        if (null === $this->_resolveDisabledBackOrderedPdp) {
            $this->_resolveDisabledBackOrderedPdp = Mage::helper('resolve')->isDisableProductBackOrdered() ||
                    Mage::helper('resolve')->isDisableModuleFunctionality() ||
                    !$this->isVisibleOnPDP() || !Mage::helper('resolve')->isResolvePaymentMethodEnabled() ||
                    $this->_isSkipProductByType() || !$this->isProductDisabledByPaymentRestRules();
        }
        return $this->_resolveDisabledBackOrderedPdp;
    }

    /**
     * Is As Low As disabled on PLP
     *
     * @return bool
     */
    public function isAsLowAsDisabledOnPLP()
    {
        if (null === $this->_resolveDisabledBackOrderedPlp) {
            $this->_resolveDisabledBackOrderedPlp = Mage::helper('resolve')->isDisableModuleFunctionality() ||
                    !$this->isVisibleOnPLP() || !Mage::helper('resolve')->isResolvePaymentMethodEnabled();
        }
        return $this->_resolveDisabledBackOrderedPlp;
    }

    /**
     * Get resolve MFP value
     *
     * @param array $product
     * @param array $categoryItemsIds
     * @return string
     */
    public function getResolveMFPValue(array $product, array $categoryItemsIds, $cartTotal = 0)
    {
        /** @var Resolve_Resolve_Helper_Mfp $mfpHelper */
        $mfpHelper = Mage::helper('resolve/mfp');
        if ($mfpValue = $mfpHelper->getMFPFromProductALS($product)) {
            return $mfpValue;
        } elseif ($mfpValue = $mfpHelper->getMFPFromCategoriesALS($categoryItemsIds)) {
            return $mfpValue;
        } elseif ($cartTotal > 0 && $mfpHelper->isPromoIdBasedOnCartSize($cartTotal)) {
            return $mfpHelper->getPromoIdCartSizeValue();
        } elseif ($mfpHelper->isMFPValidCurrentDateALS()) {
            return $mfpHelper->getPromoIdDateRange();
        } else {
            return $mfpHelper->getPromoIdDefault();
        }
    }

    public function isProductDisabledByPaymentRestRules()
    {
        foreach ($this->getRules() as $rule){
            if ($rule->restrictByName(Resolve_Resolve_Model_Payment::METHOD_CODE)){
                $product = Mage::helper('catalog')->getProduct();

                $tempQuote = new Varien_Object; // workaround since rule expects cart items containing products, not products directly

                $allItems = array();
                $product->setQty(1); // we want to test a fixed qty of 1, but this can be made more dynamic
                $product = $product->load($product->getId());
                $product->setProductId($product->getId());
                $product->setProduct($product);
                $allItems[] = $product;
                $tempQuote->setAllItems($allItems);
                $tempQuote->setAllVisibleItems($allItems);
                $tempQuote->setSubtotal($product->getPrice());

                $isValid = (bool) $rule->validate($tempQuote);
                if ($isValid) {
                    return false;
                }
            }
        }
        return true;
    }

    public function isQuoteItemsDisabledByPaymentRestRules()
    {
        foreach ($this->getRules() as $rule){
            if ($rule->restrictByName(Resolve_Resolve_Model_Payment::METHOD_CODE)){
                $quote = Mage::helper('checkout/cart')->getQuote();

                $isValid = (bool) $rule->validate($quote);
                if ($isValid) {
                    return false;
                }
            }
        }
        return true;
    }

    public function getRules()
    {
        if (is_null($this->_allRules)){
            $this->_allRules = Mage::getModel('resolve/rule')
                ->getCollection()
                ->addFieldToFilter('is_active', 1);
            if ($this->_isAdmin()){
                $this->_allRules->addFieldToFilter('for_admin', 1);
            }

            $this->_allRules->load();
            foreach ($this->_allRules as $rule){
                $rule->afterLoad();
            }
        }

        return  $this->_allRules;
    }

    protected function _isAdmin()
    {
        if (Mage::app()->getStore()->isAdmin())
            return true;
        if (Mage::app()->getRequest()->getControllerName() == 'sales_order_create')
            return true;

        return false;
    }

    /**
     * Check is learn-more visible on ala
     *
     * @param null|Mage_Core_Model_Store $store
     * @return boolean
     */
    public function isVisibleLearnMore($store = null)
    {
        return Mage::getStoreConfig(self::RESOLVE_PROMO_LEARN_MORE, $store);
    }
}
