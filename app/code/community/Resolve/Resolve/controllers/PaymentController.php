<?php

class Resolve_Resolve_PaymentController extends Mage_Checkout_Controller_Action 
{
    public function renderPreOrderAction()
    {

        $checkoutSession = Mage::helper('resolve')->getCheckoutSession();
        $string = $this->getLayout()->createBlock('resolve/payment_redirect', 'resolve_redirect')
            ->setOrder($order)->toHtml();

        if(!Mage::getSingleton('resolve/Paymentmethod')->checkMerchant()) {
            Mage_Core_Controller_Varien_Action::_redirect('checkout', array('_secure' => $this->getRequest()->isSecure()));
            return true;
        }

        if (Mage::helper('resolve')->isXhrRequest($proxyRequest)) {
            $checkoutSession->setPreOrderRender($string);
            $result = array('redirect' => Mage::getUrl('resolve/payment/redirectPreOrder',
                array('_secure' => $this->getRequest()->isSecure())
            ));
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
        } else {
            $this->getResponse()->setBody($string);
        }
    }

    public function  confirmAction()
    {
        $quote = Mage::getSingleton('checkout/session')->getQuote();

        if($quote->getId()){
            $chargeId = $this->getRequest()->getParam('charge_id');
            $quoteid = Mage::getSingleton('checkout/session')->getQuoteId();
            $quote->collectTotals()->getPayment()->setMethod('resolve');
            $quote->collectTotals()->save();
            $service = Mage::getModel('sales/service_quote', $quote);
            $service->submitAll();
            Mage::getSingleton('checkout/session')->setLastQuoteId($quote->getId())
                ->setLastSuccessQuoteId($quote->getId())
                ->clearHelperData()
                ;
            $order = $service->getOrder();
            $order->getPayment()->getMethodInstance()->processConfirmOrder($order, $checkoutToken, $chargeId);
                $order->save();
            if($order){
                Mage::getSingleton('checkout/session')->setLastOrderId($order->getId())
                    ->setLastRealOrderId($order->getIncrementId());
            }
            $quote->setIsActive(false)->save();
        }
        $this->_redirect('checkout/onepage/success');
    }

    public function setPaymentFlagAndCheckoutAction()
    {

        Mage::helper('resolve')->getCheckoutSession()->setResolvePaymentFlag(true);
        $this->_redirectUrl(Mage::helper('checkout/url')->getCheckoutUrl());
    }
   
}