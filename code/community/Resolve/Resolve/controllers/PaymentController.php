<?php
// app/code/local/Envato/Custompaymentmethod/controllers/PaymentController.php
class Resolve_Resolve_PaymentController extends Mage_Checkout_Controller_Action 
{

    /**
     * Redirect
     */
    // public function redirectAction()
    // {
    // Mage::log('gatewayAction', null, "___order.log");
    //     $session = Mage::helper('resolve')->getCheckoutSession();
    //     // if (!$session->getLastRealOrderId()) {
    //     //     $session->addError($this->__('Your order has expired.'));
    //     //     $this->_redirect('checkout/cart');
    //     //     return;
    //     // }
    //     $order = Mage::getModel('sales/order')->loadByIncrementId($session->getLastRealOrderId());
    //     die($order->getData());
    //     $this->getResponse()
    //         ->setBody($this->getLayout()->createBlock('resolve/payment_redirect', 'resolve_redirect')
    //             ->setOrder($order)->toHtml()
    //         );
    //     // $session->unsQuoteId();
    //     $session->unsRedirectUrl();
    // }

     /**
     * Render pre order
     */
    public function renderPreOrderAction()
    {
        //after place order
        Mage::log('renderPreOrderAction', null, '___order.log');
        $order = $this->getRequest()->getParam('order');
        $quote = $this->getRequest()->getParam('quote');
        $checkoutSession = Mage::helper('resolve')->getCheckoutSession();
        $string = $this->getLayout()->createBlock('resolve/payment_redirect', 'resolve_redirect')
            ->setOrder($order)->toHtml();
            // var_dump($checkoutSession->getData());
            // $quote = Mage::getModel('checkout/session')->getQuote()->setReservedOrderId($checkoutSession->getLastRealOrderId() + 1);
            // $quote->save();
            // var_dump($quote->getData());
            // die();
            // $checkoutSession->getLastRealOrderId() + 1;
            // $modQuote->setReservedOrderId($order->getIncrementId());
        // $serializedRequest = $checkoutSession->getResolveOrderRequest();
        // $proxyRequest = unserialize($serializedRequest);
        //only reserve this order id
        // $modQuote = Mage::getModel('sales/quote')->load($quote->getId());
        // $modQuote = Mage::getModel('sales/quote')->load($quote->getId());
        // $modQuote->setReservedOrderId($order->getIncrementId());
        // $modQuote->save();
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
/**
     * Redirect pre order
     */
    // public function redirectPreOrderAction()
    // {
    //     Mage::log('redirectPreOrderAction', null, 'resolve.log');

    //     $this->getResponse()->setBody(Mage::helper('resolve')->getCheckoutSession()->getPreOrderRender());
    // }
//   public function gatewayAction() 
//   {
//     Mage::log('gatewayAction', null, "___order.log");
//     if ($this->getRequest()->get("orderId"))
//     {
//       $arr_querystring = array(
//         'flag' => 1, 
//         'orderId' => $this->getRequest()->get("orderId")
//       );
       
//       Mage_Core_Controller_Varien_Action::_redirect('resolve/payment/response', array('_secure' => false, '_query'=> $arr_querystring));
//     }
//   }
   
//   public function redirectAction() 
//   {
//     $this->loadLayout();
//     Mage::log('redirectAction', null, "___order.log");
// //     Mage::app()->getResponse()->setRedirect('https://payment.api.com') ->sendResponse();

// // return ;
//     $orderId = Mage::getSingleton('checkout/session')->getLastRealOrderId();
//     $order->loadByIncrementId($orderId);

//     $block = $this->getLayout()->createBlock('Mage_Core_Block_Template','resolve',array('template' => 'resolve/redirect.phtml'));
//     $this->getLayout()->getBlock('content')->append($block);
//     $this->renderLayout();
//   }
 

    /**
     * Confirm checkout
     */
    public function  confirmAction()
    {
        $quote = Mage::getSingleton('checkout/session')->getQuote();

        if($quote->getId()){
            $chargeId = $this->getRequest()->getParam('charge_id');
            $quoteid = Mage::getSingleton('checkout/session')->getQuoteId();
            // $quote->assignCustomer(Mage::getSingleton('customer/session')->getCustomer());
            $quote->collectTotals()->getPayment()->setMethod('resolve');
            // $quote->getPayment()->importData(array('method' => 'resolve'));
            $quote->collectTotals()->save();
            $service = Mage::getModel('sales/service_quote', $quote);
            $service->submitAll();
            Mage::getSingleton('checkout/session')->setLastQuoteId($quote->getId())
                ->setLastSuccessQuoteId($quote->getId())
                ->clearHelperData()
                ;
            $order = $service->getOrder();
            $order->setStatus('processing');
            $order->setVisibleOnFront(1);

            $order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true);
            echo $chargeId.'----confirmAction</br>';
            $order->getPayment()->getMethodInstance()->processConfirmOrder($order, $checkoutToken, $chargeId);
                $order->save();
            // $order->setStatus(Mage_Sales_Model_Order::STATE_PENDING, true)->save();
            // print_r($order->getData());
            if($order){
                Mage::getSingleton('checkout/session')->setLastOrderId($order->getId())
                    ->setLastRealOrderId($order->getIncrementId());
            }
            $quote->setIsActive(false)->save();
        }
        $this->_redirect('checkout/onepage/success');
    }

    /**
     * Set Resovle Payment Flag And Checkout
     */
    public function setPaymentFlagAndCheckoutAction()
    {

        Mage::helper('resolve')->getCheckoutSession()->setResolvePaymentFlag(true);
        // var_dump(Mage::helper('resolve')->getCheckoutSession()->getData());
        // die();
        $this->_redirectUrl(Mage::helper('checkout/url')->getCheckoutUrl());
    }
   
}