<?php
// app/code/local/Envato/Custompaymentmethod/controllers/PaymentController.php
class Resolve_Resolve_PaymentController extends Mage_Checkout_Controller_Action 
{

    /**
     * Redirect
     */
    public function redirectAction()
    {
    Mage::log('gatewayAction', null, "___order.log");
        $session = Mage::helper('resolve')->getCheckoutSession();
        // if (!$session->getLastRealOrderId()) {
        //     $session->addError($this->__('Your order has expired.'));
        //     $this->_redirect('checkout/cart');
        //     return;
        // }
        $order = Mage::getModel('sales/order')->loadByIncrementId($session->getLastRealOrderId());
        $this->getResponse()
            ->setBody($this->getLayout()->createBlock('resolve/payment_redirect', 'resolve_redirect')
                ->setOrder($order)->toHtml()
            );
        $session->unsQuoteId();
        $session->unsRedirectUrl();
    }

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
        $serializedRequest = $checkoutSession->getResolveOrderRequest();
        $proxyRequest = unserialize($serializedRequest);
        //only reserve this order id
        $modQuote = Mage::getModel('sales/quote')->load($quote->getId());
        $modQuote->setReservedOrderId($order->getIncrementId());
        $modQuote->save();
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
    public function redirectPreOrderAction()
    {
        Mage::log('redirectPreOrderAction', null, 'resolve.log');

        $this->getResponse()->setBody(Mage::helper('resolve')->getCheckoutSession()->getPreOrderRender());
    }
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
 
  public function responseAction() 
  {
    Mage::log('responseAction', null, "___order.log");

    if ($this->getRequest()->get("flag") == "1" && $this->getRequest()->get("orderId")) 
    {
      $orderId = $this->getRequest()->get("orderId");
      $order = Mage::getModel('sales/order')->loadByIncrementId($orderId);
      $order->setState(Mage_Sales_Model_Order::STATE_PAYMENT_REVIEW, true, 'Payment Success.');
      $order->save();
       
      Mage::getSingleton('checkout/session')->unsQuoteId();
      Mage_Core_Controller_Varien_Action::_redirect('checkout/onepage/success', array('_secure'=> false));
    }
    else
    {
      Mage_Core_Controller_Varien_Action::_redirect('checkout/onepage/error', array('_secure'=> false));
    }
  }

    /**
     * Confirm checkout
     */
    public function confirmAction123()
    {
        $quote = Mage::getSingleton('checkout/session')->getQuote();
        $quote->getPayment()->importData(array('method' => 'resolve'));

            $quote->collectTotals()->save();

            $service = Mage::getModel('sales/service_quote', $quote);
            $service->submitAll();
            $order = $service->getOrder();

            $order = Mage::getModel('sales/order')->load($order->getId());

            echo $order->getIncrementId();
            $order->setState(Mage_Sales_Model_Order::STATE_PAYMENT_REVIEW, true, 'Payment Success.');
            $order->save();
            
            $session = Mage::getSingleton('checkout/session');
                $session->setLastQuoteId($session->getQuote()->getId())
                        ->setLastSuccessQuoteId($session->getQuote()->getId())
                        ->clearHelperData();
                        

            


                if ($order) {
                    $session->setLastOrderId($order->getId())
                                       ->setLastRealOrderId($order->getIncrementId())
                                       ->setLastOrderStatus($order->getStatus());
                }
                $session->addSuccess($this->__('This order was already completed.'));

                $this->_redirect('checkout/onepage/success');
    }

    public function  confirmAction()
    {
        $quote = Mage::getSingleton('checkout/session')->getQuote();

        if($quote->getId()){

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
            $chargeId = '0T2WECGDMAH9FURUDU';
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
   
}