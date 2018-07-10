<?php
// app/code/local/Envato/Custompaymentmethod/Helper/Data.php
class Resolve_Resolve_Helper_Data extends Mage_Core_Helper_Abstract
{
 
  /**
     * Path to title plain text enabler
     */
    const RESOLVE_PLAIN_TEXT_ENABLED = 'payment/resolve/plain_text_title_enabled';

    /**
     * Path to label html
     */
    const LABEL_HTML_CUSTOM = 'payment/resolve/label_html_custom';

    /**
     * Payment resolve XHR checkout
     */
    const PAYMENT_RESOLVE_XHR_CHECKOUT = 'payment/resolve/detect_xhr_checkout';

    /**
     * Min order threshold
     */
    const PAYMENT_RESOLVE_MIN_ORDER_TOTAL = 'payment/resolve/min_order_total';

    /**
     * Max order threshold
     */
    const PAYMENT_RESOLVE_MAX_ORDER_TOTAL = 'payment/resolve/max_order_total';

    /**
     * Pre order
     */
    const PAYMENT_RESOLVE_PRE_ORDER = 'payment/resolve/pre_order';

    /**
     * Disable for back ordered items
     */
    const PAYMENT_RESOLVE_DISABLE_BACK_ORDERED_ITEMS = 'payment/resolve/disable_for_backordered_items';

    /**
     * Checkout Flow Type
     */
    const PAYMENT_RESOLVE_CHECKOUT_FLOW_TYPE = 'payment/resolve/checkout_flow_type';

    /**
     * Disabled module
     *
     * @var bool
     */
    protected $_disabledModule;

    /**
     * Disabled back ordered on cart
     *
     * @var bool
     */
    protected $_disabledBackOrderedCart;

    /**
     * Disabled back ordered on PDP
     *
     * @var bool
     */
    protected $_disabledBackOrderedPdp;


    public function getPaymentGatewayUrl() 
    {
      return Mage::getUrl('resolve/payment/gateway', array('_secure' => false));
    }
  
    /**
     * Returns extension version
     *
     * @return string
     */
    public function getExtensionVersion()
    {
        return (string)Mage::getConfig()->getNode()->modules->Resolve_Resolve->version;
    }

    /**
     * Returns is disable for back ordered items
     *
     * @param Mage_Core_Model_Store $store
     * @return bool
     */
    public function isDisableForBackOrderedItems($store = null)
    {
        if($store == null) {
            $store = Mage::app()->getStore()->getStoreId();
        }
        return Mage::getStoreConfigFlag(self::PAYMENT_RESOLVE_DISABLE_BACK_ORDERED_ITEMS, $store);
    }

    /**
     * Returns is enabled plain text
     *
     * @param Mage_Core_Model_Store $store
     * @return bool
     */
    public function isPlainTextEnabled($store = null)
    {
        if($store == null) {
            $store = Mage::app()->getStore()->getStoreId();
        }
        return Mage::getStoreConfigFlag(self::RESOLVE_PLAIN_TEXT_ENABLED, $store);
    }

    /**
     * Check is base currency non dollar
     *
     * @param Mage_Payment_Model_Method_Abstract $method
     * @return bool
     */
    public function isNonDollarCurrencyStore($method)
    {
        return !in_array(Mage::app()->getStore()->getBaseCurrencyCode(), $method->getAcceptedCurrencyCodes());
    }

    /**
     * Is disable module functionality
     *
     * @return string
     */
    public function isDisableModuleFunctionality()
    {
        if (null === $this->_disabledModule) {
            $payments = Mage::getSingleton('payment/config')->getAllMethods();
            $method = $payments[Resolve_Resolve_Model_Paymentmethod::METHOD_CODE];
            $this->_disabledModule = $this->isNonDollarCurrencyStore($method);
        }
        return $this->_disabledModule;
    }

    /**
     * Returns html of label
     *
     * @param Mage_Core_Model_Store $store
     * @return string
     */
    public function getLabelHtmlAfter($store = null)
    {
        if($store == null) {
            $store = Mage::app()->getStore()->getStoreId();
        }
        return Mage::getStoreConfig(self::LABEL_HTML_CUSTOM, $store);
    }

    /**
     * Get module version
     *
     * @return string
     */
    public function getModuleConfigVersion()
    {
        return Mage::getConfig()->getModuleConfig('Resolve_Resolve')->version;
    }

    /**
     * Get api url
     *
     * @param Mage_Core_Model_Store $store
     * @return string
     */
    public function getApiUrl()
    {
        return Mage::getSingleton('resolve/credential')->getApiUrl();
    }

    /**
     * Get api key
     *
     * @param Mage_Core_Model_Store $store
     * @return string
     */
    public function getApiKey($store = null)
    {
        if($store == null) {
            $store = Mage::app()->getStore()->getStoreId();
        }
        return Mage::getSingleton('resolve/credential')->getApiKey($store);
    }

    /**
     * Get secret key
     *
     * @param Mage_Core_Model_Store $store
     * @return string
     */
    public function getSecretKey($store = null)
    {
        if($store == null) {
            $store = Mage::app()->getStore()->getStoreId();
        }
        return Mage::getSingleton('resolve/credential')->getSecretKey($store);
    }

    /**
     * Get detect xhr checkout
     *
     * @param Mage_Core_Model_Store $store
     * @return string
     */
    public function getDetectXHRCheckout($store = null)
    {
        if($store == null) {
            $store = Mage::app()->getStore()->getStoreId();
        }
        return Mage::getStoreConfig(self::PAYMENT_RESOLVE_XHR_CHECKOUT, $store);
    }

    /**
     * Get min order total threshold
     *
     * @param Mage_Core_Model_Store $store
     * @return string
     */
    public function getMinOrderThreshold($store = null)
    {
        if($store == null) {
            $store = Mage::app()->getStore()->getStoreId();
        }
        return Mage::getStoreConfig(self::PAYMENT_RESOLVE_MIN_ORDER_TOTAL, $store);
    }

    /**
     * Get max order total threshold
     *
     * @param Mage_Core_Model_Store $store
     * @return string
     */
    public function getMaxOrderThreshold($store = null)
    {
        if($store == null) {
            $store = Mage::app()->getStore()->getStoreId();
        }
        return Mage::getStoreConfig(self::PAYMENT_RESOLVE_MAX_ORDER_TOTAL, $store);
    }

    /**
     * Check is pre order
     *
     * @param Mage_Core_Model_Store $store
     * @return string
     */
    public function isPreOrder($store = null)
    {
        if($store == null) {
            $store = Mage::app()->getStore()->getStoreId();
        }
        return Mage::getStoreConfig(self::PAYMENT_RESOLVE_PRE_ORDER, $store);
    }

    /**
     * Checkout flow type
     *
     * @param Mage_Core_Model_Store $store
     * @return string
     */
    public function getCheckoutFlowType($store = null)
    {
        if($store == null) {
            $store = Mage::app()->getStore()->getStoreId();
        }
        return Mage::getStoreConfig(self::PAYMENT_RESOLVE_CHECKOUT_FLOW_TYPE, $store);
    }

    /**
     * Is Checkout flow type Modal
     *
     * @param Mage_Core_Model_Store $store
     * @return bool
     */
    public function isCheckoutFlowTypeModal($store = null)
    {
        $configCheckoutType = Mage::helper('resolve')->getCheckoutFlowType();
        if ($configCheckoutType == Resolve_Resolve_Model_Paymentmethod::CHECKOUT_FLOW_MODAL) {
            return true;
        } else {
            return false;
        }
    }

  
    /**
     * Is xhr request
     *
     * @param array $proxyRequest
     * @return bool
     */
    public function isXhrRequest($proxyRequest)
    {
        $detectedXhr = isset($proxyRequest['xhr']) && $proxyRequest['xhr'];
        $configXhr = Mage::helper('resolve')->getDetectXHRCheckout();
        if ($configXhr == Resolve_Resolve_Model_Paymentmethod::CHECKOUT_REDIRECT) {
            return false;
        } elseif ($configXhr == Resolve_Resolve_Model_Paymentmethod::CHECKOUT_XHR) {
            return true;
        } else {
            return $detectedXhr;
        }
    }

    /**
     * Get resolve checkout token
     *
     * @return string
     */
    public function getResolveTokenCode()
    {
        return Mage::registry('resolve_token_code');
    }

    /**
     * Check is resolve payment method is available
     *
     * @return bool
     */
    public function isResolvePaymentMethodAvailable()
    {
        $isAvailable = false;
        $method = $this->getResolvePaymentMethod();
        if ($method) {
            $isAvailable = $method->isAvailable(Mage::helper('checkout/cart')->getQuote());
        }
        return $isAvailable;
    }

    /**
     * Check is resolve payment method is enabled
     *
     * @return bool
     */
    public function isResolvePaymentMethodEnabled()
    {
        $method = $this->getResolvePaymentMethod();
        $isEnabled = $method ? $method->canUseCheckout() : false;
        return $isEnabled;
    }

    /**
     * Get resolve payment method
     *
     * @return Resolve_Resolve_Model_Paymentmethod}null
     */
    public function getResolvePaymentMethod()
    {
        $payments = Mage::getSingleton('payment/config')->getActiveMethods();
        $method = isset($payments[Resolve_Resolve_Model_Paymentmethod::METHOD_CODE])
            ? $payments[Resolve_Resolve_Model_Paymentmethod::METHOD_CODE]
            : null;
        return $method;
    }

    /**
     * Get checkout session
     *
     * @return Mage_Checkout_Model_Session
     */
    public function getCheckoutSession()
    {
        return Mage::getSingleton('checkout/session');
    }

    /**
     * Skip promo messages for back ordered products PDP
     *
     * @return bool
     */
    public function isDisableProductBackOrdered()
    {
        if (null === $this->_disabledBackOrderedPdp) {
            $this->_disabledBackOrderedPdp = false;
            if (!Mage::helper('resolve')->isDisableForBackOrderedItems()) {
                $this->_disabledBackOrderedPdp = false;
                return $this->_disabledBackOrderedPdp;
            }
            $product = Mage::helper('catalog')->getProduct();
            if ($product && $product->getId()) {
                if ($product->isGrouped()) {
                    $associatedProducts = $product->getTypeInstance(true)->getAssociatedProducts($product);
                    foreach ($associatedProducts as $associatedProduct) {
                        $inventory = Mage::getModel('cataloginventory/stock_item')->loadByProduct($associatedProduct);
                        if ($inventory->getBackorders() && ($inventory->getQty() < 1)) {
                            $this->_disabledBackOrderedPdp = true;
                            break;
                        }
                    }
                } else {
                    $inventory = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product);
                    $this->_disabledBackOrderedPdp = $inventory->getBackorders() && ($inventory->getQty() < 1);
                }
                Mage::register('resolve_disabled_backordered', $this->_disabledBackOrderedPdp);
                return $this->_disabledBackOrderedPdp;
            }
        }
        return $this->_disabledBackOrderedPdp;
    }


    /**
     * Skip promo message for back ordered products cart
     *
     * @param null $quote
     * @return bool
     */
    public function isDisableQuoteBackOrdered($quote = null)
    {
        if (null === $this->_disabledBackOrderedCart) {
            if (!Mage::helper('resolve')->isDisableForBackOrderedItems()) {
                $this->_disabledBackOrderedCart = false;
                return $this->_disabledBackOrderedCart;
            }
            if (null === $quote) {
                $quote = Mage::helper('checkout/cart')->getQuote();
            }
            foreach ($quote->getAllItems() as $quoteItem) {
                $inventory = Mage::getModel('cataloginventory/stock_item')->loadByProduct($quoteItem->getProduct());
                if ($inventory->getBackorders() && (($inventory->getQty() - $quoteItem->getQty()) < 0)) {
                    $this->_disabledBackOrderedCart = true;
                    break;
                }
            }
            Mage::register('resolve_disabled_backordered', $this->_disabledBackOrderedCart);
        }
        return $this->_disabledBackOrderedCart;
    }

    /**
     * Get product on PDP
     *
     * @return Mage_Catalog_Model_Product|null
     */
    public function getProduct()
    {
        return Mage::helper('catalog')->getProduct();
    }

    /**
     * Is product configurable
     *
     * @return bool
     */
    public function isProductConfigurable()
    {
        if ($this->getProduct() && $this->getProduct()->getId()) {
            return $this->getProduct()->isConfigurable() && $this->isDisableForBackOrderedItems();
        }
        return false;
    }

    /**
     * Get configurable back ordered info
     *
     * @return string
     */
    public function getConfigurableBackOrderedInfo()
    {
        $childProducts = Mage::getModel('catalog/product_type_configurable')
            ->getUsedProducts(null, $this->getProduct());
        $configurableAttributes = $this->getProduct()->getTypeInstance(true)
            ->getConfigurableAttributesAsArray($this->getProduct());
        $result = array();
        foreach ($childProducts as $childProduct) {
            foreach ($configurableAttributes as $configurableAttribute) {
                $result[$childProduct->getEntityId()][$configurableAttribute['attribute_id']] =
                    $childProduct[$configurableAttribute['attribute_code']];
            }
            $inventory = Mage::getModel('cataloginventory/stock_item')->loadByProduct($childProduct);
            $result[$childProduct->getEntityId()]['backorders'] = $inventory->getBackorders() &&
                ($inventory->getQty() < 1);
        }
        return Mage::helper('core')->jsonEncode($result);
    }

    /**
     * Get assets url
     *
     * @param Mage_Core_Model_Store $store
     * @return string
     */
    public function getResolveAssetsUrl()
    {
        $prefix = "cdn-assets";
        $domain = "resolve.com";
        $assetPath = "images/banners";
        return 'https://' . $prefix . '.' . $domain . '/' . $assetPath ;
    }

    /**
     * Get template for button in order review page if resolve method was selected and checkout flow type is modal
     *
     * @param string $name template name
     * @param string $block buttons block name
     * @return string
     */
    public function getReviewButtonTemplate($name, $block)
    {
        $quote = Mage::getSingleton('checkout/session')->getQuote();
        
        if ($quote) {
            $payment = $quote->getPayment();
            if ($payment && ($payment->getMethod() == Resolve_Resolve_Model_Paymentmethod::METHOD_CODE)) {
                return $name;
            }
        }

        if ($blockObject = Mage::getSingleton('core/layout')->getBlock($block)) {
            return $blockObject->getTemplate();
        }

        return '';
    }

    /**
     * Get resolve modal checkout js
     *
     * @return string
     */
    public function getResolveCheckoutJsScript()
    {
        if (Mage::helper('resolve')->isCheckoutFlowTypeModal()) {
            return 'js/resolve/checkout.js';
        }
        return '';
    }

    /**
     * Returns a checkout object instance
     *
     * @return Mage_Checkout_Model_Type_Onepage
     */
    public function _getCheckout()
    {
        return Mage::getSingleton('checkout/type_onepage');
    }

    /**
     * Get OPC save order URL
     *
     * @return string
     */
    public function getOPCCheckoutUrl()
    {
        $paramHttps = (Mage::app()->getStore()->isCurrentlySecure()) ? array('_forced_secure' => true) : array();
        return Mage::getUrl('checkout/onepage/saveOrder/form_key/' . Mage::getSingleton('core/session')->getFormKey(), $paramHttps);
    }

    public function getAllGroups()
    {
        $customerGroups = Mage::getResourceModel('customer/group_collection')
            ->load()->toOptionArray();

        $found = false;
        foreach ($customerGroups as $group) {
            if ($group['value']==0) {
                $found = true;
            }
        }
        if (!$found) {
            array_unshift($customerGroups, array('value'=>0, 'label'=>Mage::helper('salesrule')->__('NOT LOGGED IN')));
        }

        return $customerGroups;
    }
    
    public function getAllMethods()
    {
        $hash = array();
        foreach (Mage::getStoreConfig('payment') as $code=>$config){
            if (!empty($config['title'])){
                $label = '';
                if (!empty($config['group'])){
                    $label = ucfirst($config['group']) . ' - ';
                }
                $label .= $config['title'];
                /*if (!empty($config['allowspecific']) && !empty($config['specificcountry'])){
                    $label .= ' in ' . $config['specificcountry'];
                }*/
                $hash[$code] = $label;

            }
        }
        asort($hash);

        $methods = array();
        foreach ($hash as $code => $label){
            $methods[] = array('value' => $code, 'label' => $label);
        }

        return $methods;
    }

    public function getStatuses()
    {
        return array(
            '1' => Mage::helper('salesrule')->__('Active'),
            '0' => Mage::helper('salesrule')->__('Inactive'),
        );
    }
    protected function _getQuote($quoteId)
    {
        return Mage::getModel('sales/quote')->load($quoteId);
    }
    public function restoreQuote()
    {
        $order = $this->getCheckoutSession()->getLastRealOrder();
        if ($order->getId()) {
            $quote = $this->_getQuote($order->getQuoteId());
            if ($quote->getId()) {
                $quote->setIsActive(1)
                    ->setReservedOrderId(null)
                    ->save();
                $this->getCheckoutSession()
                    ->replaceQuote($quote)
                    ->unsLastRealOrderId();
                return true;
            }
        }
        return false;
    }
}