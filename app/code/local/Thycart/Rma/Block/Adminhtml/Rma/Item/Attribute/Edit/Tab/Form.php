<?php 
class Thycart_Rma_Block_Adminhtml_Rma_Item_Attribute_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $id = $this->getRequest()->getParam('id');
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
        'values'    => array('0' => 'Store View','1' => 'Website', '2' => 'Global'),
        ));
        
        $fieldset->addField('is_unique', 'select', array(
        'label'     => Mage::helper('rma')->__('Unique Value'),
        'name'      => 'is_unique',
        'values'    => array('0' => 'No','1' => 'Yes'),
        'after_element_html' => '<small>Not shared with other Products</small>',
        ));
        
        $fieldset->addField('is_required', 'select', array(
        'label'     => Mage::helper('rma')->__('Value Required'),
        'name'      => 'is_required',
        'values'    => array('0' => 'No','1' => 'Yes'),
        ));
        
        if($id)
        {
            $form->getElement('attribute_code')->setDisabled(1);
        }

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