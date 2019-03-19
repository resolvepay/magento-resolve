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
 * Class Resolve_Resolve_Model_Order_Observer_BeforeOrderSaveAdmin
 */
class Resolve_Resolve_Model_Order_Observer_BeforeOrderSaveAdmin
{
    /**
     * Before order create in admin
     * redirect(forward) to error page if payment is resolve
     */
    public function execute()
    {
        $request = Mage::app()->getRequest();
        $paymentData = $request->getPost('payment');
        $telesalesEnabled = Mage::helper('core')->isModuleEnabled('Resolve_Telesales');
        if ($paymentData && isset($paymentData['method']) &&
            $paymentData['method'] == Resolve_Resolve_Model_Payment::METHOD_CODE &&
            !$telesalesEnabled
        ) {
            $request->initForward()
                ->setModuleName('admin')
                ->setControllerName('resolve')
                ->setActionName('errorOrderCreatePageOnResolvePayment')
                ->setDispatched(false);
            return false;
        }
    }
}
