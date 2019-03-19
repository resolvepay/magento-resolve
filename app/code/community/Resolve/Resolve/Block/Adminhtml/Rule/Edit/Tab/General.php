<?php
class Resolve_Resolve_Block_Adminhtml_Rule_Edit_Tab_General extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        
        /* @var $hlp Resolve_Resolve_Helper_Data */
        $hlp = Mage::helper('resolve');
    
        $fldInfo = $form->addFieldset('general', array('legend'=> $hlp->__('General')));
        $fldInfo->addField('name', 'text', array(
            'label'     => $hlp->__('Rule Name'),
            'required'  => true,
            'name'      => 'name',
        ));
        $fldInfo->addField('is_active', 'select', array(
            'label'     => Mage::helper('salesrule')->__('Status'),
            'name'      => 'is_active',
            'options'    => $hlp->getStatuses(),
        ));  
            
        $fldInfo->addField('methods', 'multiselect', array(
            'label'     => $hlp->__('Disable Selected Payment Methods'),
            'name'      => 'methods[]',
            'values'    => $hlp->getAllMethods(),
            'required'  => true,
        ));
		
		$fldInfo->addField('cust_groups', 'multiselect', array(
            'name'      => 'cust_groups[]',
            'label'     => $hlp->__('Customer Groups'),
            'values'    => $hlp->getAllGroups(),
            'note'      => $hlp->__('Leave empty or select all to apply the rule to any group'),
			'required'  => true,
        ));
		
		$fldInfo->addField('stores', 'multiselect', array(
            'label'     => $hlp->__('Stores'),
            'name'      => 'stores[]',
            'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(),
            'note'      => $hlp->__('Leave empty or select all to apply the rule to any store'), 
			'required'  => true,
        ));             
        
        //set form values
        $form->setValues(Mage::registry('resolve_rule')->getData());
        
        return parent::_prepareForm();
    }
}