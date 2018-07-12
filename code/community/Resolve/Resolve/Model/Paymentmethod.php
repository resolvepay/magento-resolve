<?php
// app/code/local/Envato/Custompaymentmethod/Model/Paymentmethod.php
class Resolve_Resolve_Model_Paymentmethod extends Mage_Payment_Model_Method_Abstract {
  


 
    /**#@+
     * Define constants
     */
    const API_CHARGES_PATH = '/api/v2/charges/';
    const API_CHECKOUT_PATH = '/api/v2/checkout/';
    const CHECKOUT_XHR_AUTO = 'auto';
    const CHECKOUT_XHR = 'xhr';
    const CHECKOUT_REDIRECT = 'redirect';
    const CHECKOUT_FLOW_REDIRECT = 'redirect';
    const CHECKOUT_FLOW_MODAL = 'modal';
    /**#@-*/

    
    /**
     * Form block type
     */
    protected $_formBlockType = 'resolve/payment_form';  
    /**
     * Info block type
     */
    protected $_infoBlockType = 'resolve/payment_info';
    /**#@+
     * Define constants
     */
    const CHECKOUT_TOKEN = 'checkout_token';
    const METHOD_CODE = 'resolve';
    /**#@-*/

    /**
     * Code
     *
     * @var string
     */
    protected $_code = self::METHOD_CODE;

    /**#@+
     * Availability options
     */
    protected $_isGateway               = true;
    protected $_canAuthorize            = true;
    protected $_canCapture              = true;
    protected $_canCapturePartial       = false;
    protected $_canRefund               = true;
    protected $_canRefundInvoicePartial = true;
    protected $_canVoid                 = true;
    protected $_canUseInternal          = true;
    protected $_canUseCheckout          = true;
    protected $_canUseForMultishipping  = false;
    protected $_canSaveCc               = false;
    protected $_canFetchTransactionInfo = true;
    protected $_allowCurrencyCode       = array('USD');
    /**#@-*/

    protected $_resolveHelperClass = 'resolve';

    /**
     * Check method for processing with base currency
     *
     * @param string $currencyCode
     * @return bool
     */
    public function canUseForCurrency($currencyCode)
    {
        if (!in_array($currencyCode, $this->getAcceptedCurrencyCodes())) {
            return false;
        }
        return true;
    }

    /**
     * Is needed initialize
     *
     * @return bool
     */
    public function isInitializeNeeded()
    {
        if ($this->getCheckoutToken()) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Return array of currency codes supplied by Payment Gateway
     *
     * @return array
     */
    public function getAcceptedCurrencyCodes()
    {
        if (!$this->hasData('_accepted_currency')) {
            $acceptedCurrencyCodes = $this->_allowCurrencyCode;
            $acceptedCurrencyCodes[] = $this->getConfigData('currency');
            $this->setData('_accepted_currency', $acceptedCurrencyCodes);
        }
        return $this->_getData('_accepted_currency');
    }

    /**
     * Get charge ID
     *
     * @return string
     */
    public function getChargeId()
    {
        return $this->getInfoInstance()->getAdditionalInformation('charge_id');
    }

    /**
     * Get checkout token
     *
     * @return string
     */
    public function getCheckoutToken()
    {
        return $this->getInfoInstance()->getAdditionalInformation(self::CHECKOUT_TOKEN);
    }

    /**
     * Set charge Id
     *
     * @param string $chargeId
     * @return Mage_Payment_Model_Info
     */
    public function setChargeId($chargeId)
    {
        return $this->getInfoInstance()->setAdditionalInformation('charge_id', $chargeId);
    }

    /**
     * Get base api url
     *
     * @return string
     */
    public function getBaseApiUrl()
    {
        return Mage::helper('resolve')->getApiUrl();
    }

    /**
     * Api request
     *
     * @param  mixed  $method
     * @param  string $path
     * @param  null|array $data
     * @param  string $resourcePath
     * @return string
     * @throws Resolve_Resolve_Exception
     */
    protected function _apiRequest($method, $path, $data = null, $storeId = null, $resourcePath = self::API_CHARGES_PATH, $chargeId)
    {

        $url = 'https://app-sandbox.paywithresolve.com/api' . $path;

        $client = new Zend_Http_Client($url, array(
            'maxredirects' => 0,
            'timeout'      => 30));
        if ($method == Zend_Http_Client::POST && $data) {
            // $client->setEncType('application/x-www-form-urlencoded');
            $json = json_encode($data);
            $client->setRawData($json, 'application/json');
        }
        $helperClass = $this->_resolveHelperClass;

        $client->setAuth(Mage::helper($helperClass)->getApiKey($storeId),
            Mage::helper($helperClass)->getSecretKey($storeId), Zend_Http_Client::AUTH_BASIC
        );
        if($method == Zend_Http_Client::PUT && $data){
            $json = json_encode($data);
            $client->setRawData($json, 'application/json')->request('PUT');
        }
        else{

            $rawResult = $client->request($method)->getRawBody();

        }
        
        // die(var_dump($client));
        try {
            $retJson = Zend_Json::decode($rawResult, Zend_Json::TYPE_ARRAY);
        } catch (Zend_Json_Exception $e) {
            throw new Resolve_Resolve_Exception(Mage::helper('resolve')->__('Invalid resolve response: '. $rawResult));
        }
        //validate to make sure there are no errors here
        if (isset($retJson['status_code'])) {
            throw new Resolve_Resolve_Exception(Mage::helper('resolve')->__('resolve error code:'.
                $retJson['status_code'] . ' error: '. $retJson['message']));
        }
        
        Mage::log($retJson, null, 'resolveData.log');
        return $retJson;
    }

    /**
     * Get checkout from tocken
     *
     * @param string $token
     * @return string
     */
    protected function _getCheckoutFromToken($token)
    {
        return $this->_apiRequest(Zend_Http_Client::GET, $token, null, null,self::API_CHECKOUT_PATH);
    }

    /**
     * Get checkout total
     *
     * @param string $token
     * @return string
     */
    protected function _getCheckoutTotalFromToken($token)
    {
        $res = $this->_getCheckoutFromToken($token);
        return $res['total'];
    }

    /**
     * Set charge result
     *
     * @param array $result
     * @throws Resolve_Resolve_Exception
     */
    protected function _setChargeResult($result)
    {
        // if (isset($result['id'])) {
            $this->setChargeId($result);
        // } else {
        //     throw new Resolve_Resolve_Exception(Mage::helper('resolve')->__('Resolve charge id not returned from call.'));
        // }
    }

    /**
     * Validate
     *
     * @param string $amount
     * @param string $resolveAmount
     * @throws Resolve_Resolve_Exception
     */
    protected function _validateAmountResult($amount, $resolveAmount)
    {
        return true;
        if ($resolveAmount != $amount) {
            throw new Resolve_Resolve_Exception(Mage::helper('resolve')->__(
                'Your cart amount has changed since starting your Resolve application. Please try again.'
                )
            );
        }
    }

    /**
     * Send capture request to gateway
     *
     * @param Varien_Object $payment
     * @param float $amount
     * @return $this|Mage_Payment_Model_Abstract
     * @throws Resolve_Resolve_Exception
     */
    public function capture(Varien_Object $payment, $amount, $chargeId)
    {
        
        if ($amount <= 0) {
            throw new Resolve_Resolve_Exception(Mage::helper('resolve')->__('Invalid amount for capture.'));
        }
        $chargeId = $this->getChargeId();
        echo $chargeId.'----capture</br>';
        // $chargeId = 'BJdbEgMQQ';
        $amountCents = Mage::helper('resolve/util')->formatCents($amount);
        if (!$chargeId) {
            if ($this->getCheckoutToken()) {
                $this->authorize($payment, $amount);
                $chargeId = $this->getChargeId();
            } else {
                throw new Resolve_Resolve_Exception(Mage::helper('resolve')->__('Charge id have not been set.'));
            }
        }
        $order = $payment->getOrder();
        if($order) {
            $storeId = $order->getStoreId();
            if($payment->getAdditionalInformation('resolve_telesales')){
                $methodInst = $payment->getMethodInstance();
                $methodInst->setHelperClass('resolve_telesales');
            }
        }
        if (!$storeId) {
            $storeId = null;
        }
        Mage::log('capture', null, '__capture___.log');
        // $result = $this->_apiRequest(Varien_Http_Client::POST, "/charges/{$chargeId}/capture", null, $storeId);
        $result = $this->_apiRequest(Varien_Http_Client::POST, "/charges/{$chargeId}/capture", null, $storeId, $chargeId);
        $this->_validateAmountResult($amountCents, $result['amount']);
        $payment->setIsTransactionClosed(0);
        return $this;
    }

    /**
     * Identify is refund partial (compatibility issue for earlier CE version)
     *
     * @param Varien_Object $payment
     * @return $this
     */
    protected function _identifyPartialRefund(Varien_Object $payment)
    {
        $canRefundMore = $payment->getOrder()->canCreditmemo();
        $payment->setShouldCloseParentTransaction(!$canRefundMore);
        return $this;
    }

    /**
     * Refund capture
     *
     * @param Varien_Object $payment
     * @param float         $amount
     * @return $this|Mage_Payment_Model_Abstract
     * @throws Resolve_Resolve_Exception
     */
    public function refund(Varien_Object $payment, $amount)
    {
        if ($amount <= 0) {
            throw new Resolve_Resolve_Exception(Mage::helper('resolve')->__('Invalid amount for refund.'));
        }
        $chargeId = $this->getChargeId();
        $amountCents = Mage::helper('resolve/util')->formatCents($amount);
        if (!$chargeId) {
            throw new Resolve_Resolve_Exception(Mage::helper('resolve')->__('Charge id have not been set.'));
        }
        $order = $payment->getOrder();
        if($order) {
            $storeId = $order->getStoreId();
            if($payment->getAdditionalInformation('resolve_telesales')){
                $methodInst = $payment->getMethodInstance();
                $methodInst->setHelperClass('resolve_telesales');
            }
        }
        if (!$storeId) {
            $storeId = null;
        }
        // router.post('/refunds', auth, createRefund);
        // $result = $this->_apiRequest(Varien_Http_Client::POST, "{$chargeId}/refund", array(
        //         'amount' => $amountCents), $storeId
        // );

        $result = $this->_apiRequest(Varien_Http_Client::POST, "/refunds", 
            array('amount' => $amountCents, 'charge_id' => $chargeId), $storeId
        );

        $this->_validateAmountResult($amountCents, $result['amount']);
        $type = Mage_Sales_Model_Order_Payment_Transaction::TYPE_REFUND;
        $id = Mage::getModel('core/date')->date('His');
        if (!$id) {
            $id = 1;
        }
        $payment->setTransactionId("{$this->getChargeId()}-{$id}-{$type}")->setIsTransactionClosed(1);
        $this->_identifyPartialRefund($payment);
        return $this;
    }

    /**
     * Void
     *
     * @param Varien_Object $payment
     * @return $this|Mage_Payment_Model_Abstract
     * @throws Resolve_Resolve_Exception
     */
    public function void(Varien_Object $payment)
    {
        Mage::log('void', null, 'void.log');
        if (!$this->canVoid($payment)) {
            throw new Resolve_Resolve_Exception(Mage::helper('payment')->__('Void action is not available.'));
        }
        $chargeId = $this->getChargeId();
        if (!$chargeId) {
            throw new Resolve_Resolve_Exception(Mage::helper('resolve')->__('Charge id have not been set.'));
        }
        $order = $payment->getOrder();
        if($order) {
            $storeId = $order->getStoreId();
            if($payment->getAdditionalInformation('resolve_telesales')){
                $methodInst = $payment->getMethodInstance();
                $methodInst->setHelperClass('resolve_telesales');
            }
        }
        if (!$storeId) {
            $storeId = null;
        }
        $result = $this->_apiRequest(Varien_Http_Client::POST, "/charges/{$chargeId}/cancel", null, $storeId);
        return $this;
    }

    /**
     * Cancel (apply void if applicable)
     *
     * @param Varien_Object $payment
     * @return $this|Mage_Payment_Model_Abstract
     * @throws Resolve_Resolve_Exception
     */
    public function cancel(Varien_Object $payment)
    {
        Mage::log('cancel', null, 'cancel.log');
        if ($payment->canVoid($payment)) {
            $this->void($payment);
        };
        return parent::cancel($payment);
    }

    /**
     * Send authorize request to gateway
     *
     * @param  Varien_Object $payment
     * @param  float $amount
     * @return $this|Mage_Payment_Model_Abstract
     * @throws Resolve_Resolve_Exception
     */
    public function authorize(Varien_Object $payment, $amount, $chargeId)
    {

        echo $chargeId.'----authorize</br>';
        // echo $this->getChargeId();
        if ($amount <= 0) {
            throw new Resolve_Resolve_Exception(Mage::helper('resolve')->__('Invalid amount for authorization.'));
        }
        echo $chargeId;
        $amountCents = Mage::helper('resolve/util')->formatCents($amount);
        $token = $payment->getAdditionalInformation(self::CHECKOUT_TOKEN);
        $amountToAuthorize = $this->_getCheckoutTotalFromToken($token);
        $this->_validateAmountResult($amountCents, $amountToAuthorize);
        $order = $payment->getOrder();
        if($order) {
            $storeId = $order->getStoreId();
        }
        if (!$storeId) {
            $storeId = null;
        }
        // $result = $this->_apiRequest(Varien_Http_Client::POST, '', array(
        //         self::CHECKOUT_TOKEN => $token), $storeId, null, $chargeId
        // );

        // $this->_setChargeResult($result);
        $payment->setTransactionId($this->getChargeId())->setIsTransactionClosed(0);
        return $this;
    }

    /**
     * Instantiate state and set it to state object
     *
     * @param string $paymentAction
     * @param Varien_Object $stateObject
     * @return Mage_Payment_Model_Abstract|void
     */
    public function initialize($paymentAction, $stateObject)
    {
        $state = Mage_Sales_Model_Order::STATE_PENDING_PAYMENT;
        $stateObject->setState($state);
        $stateObject->setStatus('pending_payment');
        $stateObject->setIsNotified(false);
    }

    /**
     * Set resolve checkout token
     *
     *@param string $checkoutToken
     */
    public function setResolveCheckoutToken($checkoutToken)
    {
        $payment = $this->getInfoInstance();
        $payment->setAdditionalInformation(self::CHECKOUT_TOKEN, $checkoutToken);
    }

    /**
     * Process confirmation order (after return from resolve, pre_order=0)
     *
     * @param Mage_Sales_Model_Order $order
     * @param string $checkoutToken
     */
    public function processConfirmOrder($order, $checkoutToken, $chargeId)
    {
        $payment = $order->getPayment();
        $payment->setAdditionalInformation(self::CHECKOUT_TOKEN, $checkoutToken);
        $payment->setAdditionalInformation('resolve_telesales', false);
        $action = $this->getConfigData('payment_action');
        $this->_apiRequest("PUT", "/charges/{$chargeId}", array(
        'po_number'=>$order->getIncrementId(),'order_number'=>$order->getIncrementId()), $storeId
    );
    // echo 'Put-param ' . $key . ' = ' . $value . PHP_EOL;
        echo $chargeId . '--processConfirmOrder</br>';
        //authorize the total amount.
        $this->_setChargeResult($chargeId);
        $payment->authorize(true, self::_resolveTotal($order), 123123);
        $payment->setAmountAuthorized(self::_resolveTotal($order));
        $order->save();
       
        //can capture as well..
        if ($action == self::ACTION_AUTHORIZE_CAPTURE) {
            $payment->setAmountAuthorized(self::_resolveTotal($order));
            $payment->setBaseAmountAuthorized($order->getBaseTotalDue());
            $payment->capture(null, chargeId);
            $order->save();
        }
    }

    /**
     * Return Order place redirect url
     *
     * @return string
     */
    public function getOrderPlaceRedirectUrl()
    {
        if (!$this->redirectPreOrder()) {
            return Mage::getUrl('resolve/payment/redirect', array('_secure' => true));
        }
    }

    /**
     * Get checkout object
     *
     * @param Mage_Sales_Model_Order $order
     * @return array
     */
    public function getCheckoutObject($order)
    {
        $shippingAddress = $order->getShippingAddress();
        $shipping = null;
        if ($shippingAddress) {
            $shipping = array(
                'name' =>  $shippingAddress->getName(),
                'company_name' => 'Test Company',
                'address_line1' => $shippingAddress->getStreet(1),
                'address_line2' => $shippingAddress->getStreet(2),
                'address_city' => $shippingAddress->getCity(),
                'address_postal' => $shippingAddress->getPostcode(),
                'address_country' => $shippingAddress->getCountryModel()->getIso2Code(),
            );
        }

        $billingAddress = $order->getBillingAddress();
        $billing = array(
            'name' => $billingAddress->getName(),
            'company_name' => 'Test Company',
            'address_line1' => $billingAddress->getStreet(1),
            'address_line2' => $billingAddress->getStreet(2),
            'address_city' => $billingAddress->getCity(),
            'address_postal' => $billingAddress->getPostcode(),
            'address_country' => $billingAddress->getCountryModel()->getIso2Code(),
        );
       
        $items = array();
        $productIds = array();
        $productItemsMFP = array();
        $categoryItemsIds = array();
        foreach ($order->getAllVisibleItems() as $orderItem) {
            $productIds[] = $orderItem->getProductId();
        }
        $products = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect(
                array('resolve_product_mfp', 'resolve_product_mfp_type', 'resolve_product_mfp_priority')
            )
            ->addAttributeToFilter('entity_id', array('in' => $productIds));
        $productItems = $products->getItems();
        foreach ($order->getAllVisibleItems() as $orderItem) {
            $product = $productItems[$orderItem->getProductId()];
            if (Mage::helper('resolve')->isPreOrder() && $orderItem->getParentItem() &&
                ($orderItem->getParentItem()->getProductType() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE)
            ) {
                continue;
            }
            // $items[] = array(
            //     'sku' => $orderItem->getSku(),
            //     'display_name' => $orderItem->getName(),
            //     'item_url' => $product->getProductUrl(),
            //     'item_image_url' => $product->getImageUrl(),
            //     'qty' => intval($orderItem->getQtyOrdered()),
            //     'unit_price' => Mage::helper('resolve/util')->formatCents($orderItem->getPrice())
            // );
            $items[] = array(
                'name' => $orderItem->getName(),
                'sku' => $orderItem->getSku(),
                // 'display_name' => $orderItem->getName(),
                // 'item_url' => $product->getProductUrl(),
                // 'item_image_url' => $product->getImageUrl(),
                // 'qty' => intval($orderItem->getQtyOrdered()),
                'unit_price' => Mage::helper('resolve/util')->formatCents($orderItem->getPrice()),
                'quantity' => intval($orderItem->getQtyOrdered()),
            );
            // $start_date = $product->getResolveProductMfpStartDate();
            // $end_date = $product->getResolveProductMfpEndDate();
            // if(empty($start_date) || empty($end_date)) {
            //     $mfpValue = $product->getResolveProductMfp();
            // } else {
            //     if(Mage::app()->getLocale()->isStoreDateInInterval(null, $start_date, $end_date)) {
            //         $mfpValue = $product->getResolveProductMfp();
            //     } else {
            //         $mfpValue = "";
            //     }
            // }

            // $productItemsMFP[] = array(
            //     'value' => $mfpValue,
            //     'type' => $product->getResolveProductMfpType(),
            //     'priority' => $product->getResolveProductMfpPriority() ?
            //         $product->getResolveProductMfpPriority() : 0
            // );

            $categoryIds = $product->getCategoryIds();
            if (!empty($categoryIds)) {
                $categoryItemsIds = array_merge($categoryItemsIds, $categoryIds);
            }
        }

        // $checkout = array(
        //     'checkout_id' => $order->getIncrementId(),
        //     'currency' => $order->getOrderCurrencyCode(),
        //     'shipping_amount' => Mage::helper('resolve/util')->formatCents($order->getShippingAmount()),
        //     'shipping_type' => $order->getShippingMethod(),
        //     'tax_amount' => Mage::helper('resolve/util')->formatCents($order->getTaxAmount()),
        //     'merchant' => array(
        //         'public_api_key' => Mage::helper('resolve')->getApiKey(),
        //         'user_confirmation_url' => Mage::getUrl('resolve/payment/confirm', array('_secure' => true)),
        //         'user_cancel_url' => Mage::helper('checkout/url')->getCheckoutUrl(),
        //         'user_confirmation_url_action' => 'POST',
        //         'charge_declined_url' => Mage::helper('checkout/url')->getCheckoutUrl()
        //     ),
        //     'config' => array('required_billing_fields' => 'name,address,email'),
        //     'items' => $items,
        //     'billing' => $billing
        // );
        $checkout = array(
            'sandbox' => true,
            'merchant' => array(
                'id' => 'test',
                'success_url' => Mage::getUrl('resolve/payment/confirm', array('_secure' => true)),
                'cancel_url'=> Mage::helper('checkout/url')->getCheckoutUrl(),
            ),
            // 'checkout_id' => $order->getIncrementId(),
            // 'currency' => $order->getOrderCurrencyCode(),
            'shipping_amount' => Mage::helper('resolve/util')->formatCents($order->getShippingAddress()->getShippingAmount()),
            // 'shipping_type' => $order->getShippingAddress()->getShippingMethod(),
            'tax_amount' => Mage::helper('resolve/util')->formatCents($order->getShippingAddress()->getTaxAmount()),
            'customer_key' => md5(`123xyz:4S2ThdKn5idMmHwIZ5TOUXcz0JdAizhA`),
            'purchase_order_id' => $order->getReservedOrderId(),
            'order_id' => $order->getReservedOrderId(),
            'po_number' => $order->getReservedOrderId(),
            // 'merchant' => array(
            //     'public_api_key' => Mage::helper('resolve')->getApiKey(),
            //     'user_confirmation_url' => Mage::getUrl('resolve/payment/confirm', array('_secure' => true)),
            //     'user_cancel_url' => Mage::helper('checkout/url')->getCheckoutUrl(),
            //     'user_confirmation_url_action' => 'POST',
            //     'charge_declined_url' => Mage::helper('checkout/url')->getCheckoutUrl()
            // ),
            // 'config' => array('required_billing_fields' => 'name,address,email'),
            'items' => $items,
            // 'billing' => $billing
        );
        // By convention, Resolve expects positive value for discount amount. Magento provides negative.
        $discountAmtResolve = (-1) * $order->getDiscountAmount();
        if ($discountAmtResolve > 0.001) {
            $discountCode = $this->_getDiscountCode($order);
            $checkout['discounts'] = array(
                $discountCode => array(
                    'discount_amount' => Mage::helper('resolve/util')->formatCents($discountAmtResolve)
                )
            );
        }

        if ($shipping) {
            $checkout['shipping'] = $shipping;
        }
        $checkout['total_amount'] = Mage::helper('resolve/util')->formatCents(self::_resolveTotal($order));
        if (method_exists('Mage', 'getEdition')){
            $platform_edition = Mage::getEdition();
        }
        $platform_version = Mage::getVersion();
        $platform_version_edition = isset($platform_edition) ? $platform_version.' '.$platform_edition : $platform_version;
        // $checkout['metadata'] = array(
        //     'shipping_type' => $order->getShippingDescription(),
        //     'platform_type' => 'Magento',
        //     'platform_version' => $platform_version_edition,
        //     'platform_resolve' => Mage::helper('resolve')->getExtensionVersion()
        // );
        
        $checkout['customer'] = array(
            'id' => $order->getCustomerId(),
            'first_name' => $order->getCustomerFirstname(),
            'last_name' => $order->getCustomerLastname(),
            'name' => $order->getCustomerFirstname().' '.$order->getCustomerLastname() ,
            'phone' => '',
        );

        $resolveMFPValue = Mage::helper('resolve/mfp')->getResolveMFPValue($productItemsMFP, $categoryItemsIds, $order->getBaseGrandTotal());
        if ($resolveMFPValue) {
            $checkout['financing_program'] = $resolveMFPValue;
        }

        $checkoutObject = new Varien_Object($checkout);
        Mage::dispatchEvent('resolve_get_checkout_object_after', array('checkout_object' => $checkoutObject));
        $checkout = $checkoutObject->getData();

        return $checkout;
    }

    /**
     * Get checkout object from quote
     *
     * @param Mage_Sales_Model_Quote $quote
     * @return array
     */
    public function getCheckoutQuoteObject($quote)
    {
        $shippingAddress = $quote->getShippingAddress();
        $shipping = null;
        if ($shippingAddress) {
            $shipping = array(
                'name' => array('full' => $shippingAddress->getName()),
                'phone_number' => $shippingAddress->getTelephone(),
                'phone_number_alternative' => $shippingAddress->getAltTelephone(),
                'address' => array(
                    'line1'   => $shippingAddress->getStreet(1),
                    'line2'   => $shippingAddress->getStreet(2),
                    'city'    => $shippingAddress->getCity(),
                    'state'   => $shippingAddress->getRegion(),
                    'country' => $shippingAddress->getCountryModel()->getIso2Code(),
                    'zipcode' => $shippingAddress->getPostcode(),
                ));
        }

        $billingAddress = $quote->getBillingAddress();
        $billing = array(
            'name' => array('full' => $billingAddress->getName()),
            'email' => $quote->getCustomerEmail(),
            'phone_number' => $billingAddress->getTelephone(),
            'phone_number_alternative' => $billingAddress->getAltTelephone(),
            'address' => array(
                'line1'   => $billingAddress->getStreet(1),
                'line2'   => $billingAddress->getStreet(2),
                'city'    => $billingAddress->getCity(),
                'state'   => $billingAddress->getRegion(),
                'country' => $billingAddress->getCountryModel()->getIso2Code(),
                'zipcode' => $billingAddress->getPostcode(),
            ));

        $items = array();
        $productIds = array();
        $productItemsMFP = array();
        $categoryItemsIds = array();
        foreach ($quote->getAllVisibleItems() as $orderItem) {
            $productIds[] = $orderItem->getProductId();
        }
        $products = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect(
                array('resolve_product_mfp', 'resolve_product_mfp_type', 'resolve_product_mfp_priority')
            )
            ->addAttributeToFilter('entity_id', array('in' => $productIds));
        $productItems = $products->getItems();
        foreach ($quote->getAllVisibleItems() as $orderItem) {
            $product = $productItems[$orderItem->getProductId()];
            if (Mage::helper('resolve')->isPreOrder() && $orderItem->getParentItem() &&
                ($orderItem->getParentItem()->getProductType() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE)
            ) {
                continue;
            }
            $items[] = array(
                'name' => $orderItem->getName(),
                'sku' => $orderItem->getSku(),
                // 'display_name' => $orderItem->getName(),
                // 'item_url' => $product->getProductUrl(),
                // 'item_image_url' => $product->getImageUrl(),
                // 'qty' => intval($orderItem->getQtyOrdered()),
                'unit_price' => Mage::helper('resolve/util')->formatCents($orderItem->getPrice()),
                'quantity' => intval($orderItem->getQtyOrdered()),
            );

            $start_date = $product->getResolveProductMfpStartDate();
            $end_date = $product->getResolveProductMfpEndDate();
            if(empty($start_date) || empty($end_date)) {
                $mfpValue = $product->getResolveProductMfp();
            } else {
                if(Mage::app()->getLocale()->isStoreDateInInterval(null, $start_date, $end_date)) {
                    $mfpValue = $product->getResolveProductMfp();
                } else {
                    $mfpValue = "";
                }
            }

            $productItemsMFP[] = array(
                'value' => $mfpValue,
                'type' => $product->getResolveProductMfpType(),
                'priority' => $product->getResolveProductMfpPriority() ?
                    $product->getResolveProductMfpPriority() : 0
            );

            $categoryIds = $product->getCategoryIds();
            if (!empty($categoryIds)) {
                $categoryItemsIds = array_merge($categoryItemsIds, $categoryIds);
            }
        }

        
        $checkout = array(
            'checkout_id' => $quote->getIncrementId(),
            'currency' => $quote->getQuoteCurrencyCode(),
            'shipping_amount' => Mage::helper('resolve/util')->formatCents($quote->getShippingAmount()),
            'shipping_type' => $quote->getShippingMethod(),
            'tax_amount' => Mage::helper('resolve/util')->formatCents($quote->getTaxAmount()),
            'merchant' => array(
                'public_api_key' => Mage::helper('resolve')->getApiKey(),
                'user_confirmation_url' => Mage::getUrl('resolve/payment/confirm', array('_secure' => true)),
                'user_cancel_url' => Mage::helper('checkout/url')->getCheckoutUrl(),
                'user_confirmation_url_action' => 'POST',
                'charge_declined_url' => Mage::helper('checkout/url')->getCheckoutUrl()
            ),
            'config' => array('required_billing_fields' => 'name,address,email'),
            'items' => $items,
            'billing' => $billing
        );
        // By convention, resolve expects positive value for discount amount. Magento provides negative.
        $discountAmtResolve = (-1) * $quote->getDiscountAmount();
        if ($discountAmtResolve > 0.001) {
            $discountCode = $this->_getDiscountCode($quote);
            $checkout['discounts'] = array(
                $discountCode => array(
                    'discount_amount' => Mage::helper('resolve/util')->formatCents($discountAmtResolve)
                )
            );
        }

        if ($shipping) {
            $checkout['shipping'] = $shipping;
        }
        $checkout['total_amount'] = Mage::helper('resolve/util')->formatCents($quote->getGrandTotal());
        if (method_exists('Mage', 'getEdition')){
            $platform_edition = Mage::getEdition();
        }
        $platform_version = Mage::getVersion();
        $platform_version_edition = isset($platform_edition) ? $platform_version.' '.$platform_edition : $platform_version;
        $checkout['metadata'] = array(
            'shipping_type' => $quote->getShippingAddress()->getShippingDescription(),
            'platform_type' => 'Magento',
            'platform_version' => $platform_version_edition,
            'platform_resolve' => Mage::helper('resolve')->getExtensionVersion(),
            'mode' => 'modal'
        );
        $resolveMFPValue = Mage::helper('resolve/mfp')->getResolveMFPValue($productItemsMFP, $categoryItemsIds, $quote->getBaseGrandTotal());
        if ($resolveMFPValue) {
            $checkout['financing_program'] = $resolveMFPValue;
        }

        $checkoutObject = new Varien_Object($checkout);
        Mage::dispatchEvent('resolve_get_checkout_object_after', array('checkout_object' => $checkoutObject));
        $checkout = $checkoutObject->getData();
        return $checkout;
    }

    /**
     * Get discount code
     *
     * @param Mage_Sales_Model_Order $order
     * @return string
     */
    protected function _getDiscountCode($order)
    {
        return $order->getDiscountDescription();
    }

    /**
     * resolve total
     *
     * @param Mage_Sales_Model_Order $order
     * @return string
     */
    protected static function _resolveTotal($order)
    {
        // return $order->getTotalDue();
        return $order->getGrandTotal();
    }

    /**
     * Redirect pre-order
     *
     * @return bool
     */
    public function redirectPreOrder()
    {
        return $this->getConfigData('pre_order');
    }

    /**
     * Can use for order threshold
     *
     * @param Mage_Sales_Model_Quote $quote
     * @return bool
     */
    public function canUseForQuoteThreshold($quote)
    {
        $total = $quote->getBaseGrandTotal();
        $minTotal = $this->getConfigData('min_order_total');
        $maxTotal = $this->getConfigData('max_order_total');
        if (!empty($minTotal) && $total < $minTotal || !empty($maxTotal) && $total > $maxTotal) {
            return false;
        }
        return true;
    }

    /**
     * Check zero total
     *
     * @param Mage_Sales_Model_Quote $quote
     * @return bool
     */
    public function canUseForZeroTotal($quote)
    {
        $total = $quote->getBaseSubtotal() + $quote->getShippingAddress()->getBaseShippingAmount();
        if ($total < 0.0001 && $this->getCode() != 'free'
            && !($this->canManageRecurringProfiles() && $quote->hasRecurringItems())
        ) {
            return false;
        }
        return true;
    }

    /**
     * Can use for back ordered
     *
     * @param Mage_Sales_Model_Quote $quote
     * @return bool
     */
    public function canUseForBackOrdered($quote)
    {
        return !Mage::helper('resolve')->isDisableQuoteBackOrdered($quote);
    }

    /**
     * Is available method
     *
     * @param Mage_Sales_Model_Quote $quote
     * @return bool
     */
    public function isAvailable($quote = null)
    {
        return $this->isAvailableForQuote($quote) && parent::isAvailable($quote);
    }

    /**
     * Added verification for quote (compatibility reason)
     *
     * @param Mage_Sales_Model_Quote $quote
     * @return bool
     */
    public function isAvailableForQuote($quote = null)
    {
        if ($quote) {
            $shipCountry = $quote->getShippingAddress()->getCountry();
            if (!empty($shipCountry) && !$this->canUseForCountry($shipCountry)) {
                return false;
            }

            if (!$this->canUseForCountry($quote->getBillingAddress()->getCountry())) {
                return false;
            }

            if (!$this->canUseForCurrency($quote->getStore()->getBaseCurrencyCode())) {
                return false;
            }

            if (!$this->canUseCheckout()) {
                return false;
            }

            if (!$this->canUseForQuoteThreshold($quote)) {
                return false;
            }

            if (!$this->canUseForZeroTotal($quote)) {
                return false;
            }

            if (!$this->canUseForBackOrdered($quote)) {
                return false;
            }
        }

        return true;
    }

    public function setHelperClass($class = 'resolve'){
        $this->_resolveHelperClass = $class;
    }
}