<?php
// app/code/local/Envato/Custompaymentmethod/Block/Info/Custompaymentmethod.php
class Resolve_Resolve_Block_Payment_Info extends Mage_Payment_Block_Info
{
    /**
     * Url of site
     */
    const RESOLVE_SITE_URL = 'https://www.paywithresolve.com/';

    /**
     * Set custom template
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setData('area','frontend');
        $this->setTemplate('resolve/info/resolve.phtml');
    }

    /**
     * Get Resolve site url
     *
     * @return string
     */
    public function getResolveSiteUrl()
    {
        return self::RESOLVE_SITE_URL;
    }
}