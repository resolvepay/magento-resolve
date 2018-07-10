<?php

class Resolve_Resolve_Model_Source_PaymentAction
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
                'value' => Resolve_Resolve_Model_Paymentmethod::ACTION_AUTHORIZE,
                'label' => Mage::helper('resolve')->__('Authorize Only')
            ),
            array(
                'value' => Resolve_Resolve_Model_Paymentmethod::ACTION_AUTHORIZE_CAPTURE,
                'label' => Mage::helper('resolve')->__('Authorize and Capture')
            ),
        );
    }
}
