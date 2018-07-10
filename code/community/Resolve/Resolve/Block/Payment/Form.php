<?php
// app/code/local/Envato/Custompaymentmethod/Block/Form/Custompaymentmethod.php
class Resolve_Resolve_Block_Payment_Form extends Mage_Payment_Block_Form
{
  protected function _construct()
  {
    parent::_construct();
    $this->setTemplate('resolve/form/payment.phtml');
    // $this->_replaceLabel();
  }
  protected function _replaceLabel()
  {
    //   if (!$this->helper('resolve')->isPlainTextEnabled()) {
          $this->setMethodTitle('');
          
        //   $html = $this->helper('resolve')->getLabelHtmlAfter();
          $html = "<img src='https://static1.squarespace.com/static/59452ce2440243759f601679/t/59a4dd4ff7e0abcd30dcf3d3/1529556634211/?format=1500w'/>";
          $this->setMethodLabelAfterHtml($html);
    //   }
  }
}