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
 * Resolve checkout button link
 */
class Resolve_Resolve_Block_Promo_CheckoutButton extends Mage_Core_Block_Template
{
    /**
     * Is button enabled
     *
     * @var bool
     */
    protected $_isEnabled;

    /**
     * Is checkout button enabled to display
     *
     * @return bool
     */
    public function isEnabled()
    {
        if (null === $this->_isEnabled) {
            $method = $this->helper('resolve')->getResolvePaymentMethod();
            $isEnabled = !$this->helper('resolve')->isDisableModuleFunctionality() &&
                // $this->helper('resolve/promo_data')->isCheckoutButtonActive() &&
                $this->helper('resolve')->isResolvePaymentMethodEnabled() &&
                $method->canUseForQuoteThreshold($this->helper('checkout/cart')->getQuote()) &&
                !Mage::registry('resolve_disabled_backordered');
            $this->_isEnabled = $isEnabled;
        }
        return $this->_isEnabled;
    }

    /**
     * Render the block if needed
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (!$this->isEnabled()) {
            return '';
        }
        return parent::_toHtml();
    }

    /**
     * Get checkout url
     *
     * @return string
     */
    public function getCheckoutUrl()
    {
        return Mage::getUrl('resolve/payment/setPaymentFlagAndCheckout');
    }
}
