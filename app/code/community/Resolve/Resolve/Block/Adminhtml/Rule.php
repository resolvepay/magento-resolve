<?php
class Resolve_Resolve_Block_Adminhtml_Rule extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_rule';
        $this->_blockGroup = 'resolve';
        $this->_headerText = Mage::helper('resolve')->__('Payment Restriction Rules');
        $this->_addButtonLabel = Mage::helper('resolve')->__('Add Rule');
        parent::__construct();
    }
}