<?php 
class Thycart_Rma_Block_Adminhtml_Rma_Item_Attribute_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset("attribute_form", array("legend"=>Mage::helper("rma")->__("General information")));
        
        $fieldset->addField('attribute_code', 'text', array(
        'label'     => Mage::helper('rma')->__('Attribute Code '),
        'name'      => 'attribute_code',
        'required'  => true,
        'class'     => 'required-entry validate-no-html-tags validate-code',
        ));

        $fieldset->addField('scope', 'select', array(
        'label'     => Mage::helper('rma')->__('Scope'),
        'name'      => 'scope',
        'required'  => true,
        'values'    => array('1' => 'Store View','2' => 'Website', '3' => 'Global'),
        ));

        if (Mage::getSingleton("adminhtml/session")->getAttributeData())
        {
            $form->setValues(Mage::getSingleton("adminhtml/session")->getAttributeData());
            Mage::getSingleton("adminhtml/session")->setAttributeData(null);
        } 
        elseif(Mage::registry("attribute_data")) {
            $form->setValues(Mage::registry("attribute_data")->getData());
        }

    return parent::_prepareForm();
    } 

}
?>