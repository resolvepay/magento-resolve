<?php
class Resolve_Resolve_Block_Payment_Redirect extends Mage_Core_Block_Template
{
    /**
     * Set custom template
     */
    protected function _construct()
    {
        parent::_construct();
        $this->unsetData('cache_lifetime');
        $this->unsetData('cache_tags');
        $this->setTemplate('resolve/redirect.phtml');
    }
}
