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
 * Class Resolve_Resolve_Model_Order_Observer_ApplyResolvePaymentMethod
 *
 * Apply resolve payment method
 */
class Resolve_Resolve_Model_Order_Observer_ApplyResolvePaymentMethod
{
    /**
     * Apply resolve payment method
     *
     * @param Varien_Event_Observer $observer
     */
    public function execute($observer)
    {
        if ($this->_canApplyResolvePaymentMethod()) {
            $session = Mage::helper('resolve')->getCheckoutSession();
            $quote = $session->getQuote();
            $payment = $quote->getPayment();
            $data['method'] = Resolve_Resolve_Model_Payment::METHOD_CODE;
            $payment->importData($data);
            $quote->save();
            // remove payment flag from session (payment method will be set only once)
            $session->unsResolvePaymentFlag();
        }
    }

    /**
     * Check is resolve payment method can be applied
     *
     * @return bool
     */
    protected function _canApplyResolvePaymentMethod()
    {
        $canApply = Mage::helper('resolve')->getCheckoutSession()->getResolvePaymentFlag()
//                  && Mage::helper('resolve/promo_data')->isCheckoutButtonActive()
                  && Mage::helper('resolve')->isResolvePaymentMethodAvailable();
        return  $canApply;
    }
}
