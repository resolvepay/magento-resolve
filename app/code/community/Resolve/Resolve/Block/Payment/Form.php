<?php
class Resolve_Resolve_Block_Payment_Form extends Mage_Payment_Block_Form
{
  protected function _construct()
  {
    parent::_construct();
    $this->setTemplate('resolve/form/payment.phtml');
    $this->_replaceLabel();
  }
  protected function _replaceLabel()
  {
      if (!$this->helper('resolve')->isPlainTextEnabled()) {
          $this->setMethodTitle('');
          
          $html = $this->helper('resolve')->getLabelHtmlAfter();
          $this->setMethodLabelAfterHtml($html);
      }
  }
}