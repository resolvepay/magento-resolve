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
 * Class Resolve_Resolve_Model_Order_Observer_AfterSaveOrder
 *
 * After save order confirmation (return from resolve)
 */
class Resolve_Resolve_Model_Order_Observer_AfterSaveOrder
{
    /**
     * Check is resolve payment method
     *
     * @param array $proxyRequest
     * @return bool
     */
    protected function _isResolvePaymentMethod($proxyRequest)
    {
        return isset($proxyRequest['params']['payment']['method'])
            && $proxyRequest['params']['payment']['method'] == Resolve_Resolve_Model_Payment::METHOD_CODE;
    }

    /**
     * Apply resolve success logic
     *
     * @param Varien_Event_Observer $observer
     */
    public function postDispatchSaveOrder($observer)
    {
        $response = $observer->getControllerAction()->getResponse();
        $session = Mage::helper('resolve')->getCheckoutSession();
        $serializedRequest = $session->getResolveOrderRequest();
        $proxyRequest = unserialize($serializedRequest);
        $checkoutToken = Mage::registry('resolve_token_code');
        $lastOrderId = $session->getLastOrderId();
        //Return, if order was placed before confirmation
        if (!($serializedRequest && $checkoutToken) || !Mage::helper('resolve')->isXhrRequest($proxyRequest)
            || !$this->_isResolvePaymentMethod($proxyRequest) || !$lastOrderId) {
            return;
        }

        $session->setPreOrderRender(null);
        $session->setLastResolveSuccess($checkoutToken);
        $session->setResolveOrderRequest(null);
        $response->setRedirect(Mage::getUrl('checkout/onepage/success'))->sendResponse();
        return;
    }
}
