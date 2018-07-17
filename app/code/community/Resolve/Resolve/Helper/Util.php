<?php

class Resolve_Resolve_Helper_Util extends Mage_Core_Helper_Abstract
{
    const MONEY_FORMAT = "%.2f";

    protected function _formatMoney($amount)
    {
        return sprintf(self::MONEY_FORMAT, $amount);
    }

     public function formatCents($amount = 0)
    {
        $negative = false;
        $str = $this->_formatMoney($amount);
    
        return $str;
    }
}
