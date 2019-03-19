<?php
class Resolve_Resolve_Block_Adminhtml_Rule_Grid_Renderer_Methods extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Input
{
    public function render(Varien_Object $row)
    {
        /* @var $hlp Resolve_Resolve_Helper_Data */
        $hlp = Mage::helper('resolve');
        
        $v = $row->getData('methods');
        if (!$v) {
            return $hlp->__('Allows All');
        }
        $v = explode(',', $v);
        
        $html = '';
        foreach($hlp->getAllMethods() as $row)
        {
            if (in_array($row['value'], $v)){
                $html .= $row['label'] . "<br />";
            }
        }
        return $html;
    }
}