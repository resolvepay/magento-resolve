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
                'value' => Resolve_Resolve_Model_Payment::CHECKOUT_XHR_AUTO,
                'label' => Mage::helper('resolve')->__('Auto Detect')
            ),
            array(
                'value' => Resolve_Resolve_Model_Payment::CHECKOUT_XHR,
                'label' => Mage::helper('resolve')->__('Checkout uses xhr')
            ),
            array(
                'value' => Resolve_Resolve_Model_Payment::CHECKOUT_REDIRECT,
                'label' => Mage::helper('resolve')->__('Checkout uses redirect')
            )
        );
    }
}
