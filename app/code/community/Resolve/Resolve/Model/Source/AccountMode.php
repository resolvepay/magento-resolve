<?php

class Resolve_Resolve_Model_Source_AccountMode
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
                'value' => 'sandbox',
                'label' => Mage::helper('resolve')->__('Sandbox')
            ),
            array(
                'value' => 'production',
                'label' => Mage::helper('resolve')->__('Production')
            ),
        );
    }
}
