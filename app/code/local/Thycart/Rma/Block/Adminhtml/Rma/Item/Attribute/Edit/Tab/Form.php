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
        'class' => 'required-entry',
        ));

//        $fieldset->addField('is_required', 'text', array(
//        'label'     => Mage::helper('rma')->__('Required'),
//        'name'      => 'is_required',
//        'required'  => true,
//        'class' => 'required-entry',
//        ));
//        
//        $fieldset->addField('is_unique', 'text', array(
//        'label'     => Mage::helper('rma')->__('Unique'),
//        'name'      => 'is_unique',
//        'required'  => true,
//        'class' => 'required-entry',
//        ));

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