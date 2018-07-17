<?php

class Resolve_Resolve_Block_Payment_Info extends Mage_Payment_Block_Info
{
    const RESOLVE_SITE_URL = 'https://www.paywithresolve.com/';

    protected function _construct()
    {
        parent::_construct();
        $this->setData('area','frontend');
        $this->setTemplate('resolve/info/resolve.phtml');
    }

    public function getResolveSiteUrl()
    {
        return self::RESOLVE_SITE_URL;
    }
}