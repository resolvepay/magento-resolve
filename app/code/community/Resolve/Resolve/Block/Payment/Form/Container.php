<?php
/**
 * Created by PhpStorm.
 * User: opano
 * Date: 3/18/2019
 * Time: 4:43 PM
 */

class Resolve_Resolve_Block_Payment_Form_Container extends Mage_Checkout_Block_Onepage_Payment_Methods
{

    public function getMethods()
    {
        $allowedMethods = [];
        $methods = parent::getMethods();
        if ($methods)
        {
            $quote = $this->getQuote();
            foreach ($this->helper('resolve/payment_data')->validateStoreMethods($quote, $methods) as $method) {
                if ($this->_canUseMethod($method) && $method->isApplicableToQuote(
                        $quote,
                        Mage_Payment_Model_Method_Abstract::CHECK_ZERO_TOTAL
                    )) {
                    $this->_assignMethod($method);
                    $allowedMethods[] = $method;
                }
            }
            $this->setData('methods', $allowedMethods);
        }
        return $allowedMethods;
    }
}