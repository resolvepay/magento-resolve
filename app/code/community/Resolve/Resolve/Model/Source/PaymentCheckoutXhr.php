<?php

class Resolve_Resolve_Model_Source_PaymentCheckoutXhr
{
    /**
     * Options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array(
                'value' => Resolve_Resolve_Model_Paymentmethod::CHECKOUT_XHR_AUTO,
                'label' => Mage::helper('resolve')->__('Auto Detect')
            ),
            array(
                'value' => Resolve_Resolve_Model_Paymentmethod::CHECKOUT_XHR,
                'label' => Mage::helper('resolve')->__('Checkout uses xhr')
            ),
            array(
                'value' => Resolve_Resolve_Model_Paymentmethod::CHECKOUT_REDIRECT,
                'label' => Mage::helper('resolve')->__('Checkout uses redirect')
            )
        );
    }
}
