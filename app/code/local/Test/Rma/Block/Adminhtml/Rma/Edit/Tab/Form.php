<?php 
class Test_Rma_Block_Adminhtml_Rma_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {

        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset("rma_form", array("legend"=>Mage::helper("rma")->__("General information")));

        $fieldset->addField('customer_name', 'text', array(
        'label'     => Mage::helper('rma')->__('Customer Name'),
        'name'      => 'customer_name',
        'required'  => true,
        'class' => 'required-entry',
        ));

        $fieldset->addField('status', 'text', array(
                'label'     => Mage::helper('rma')->__('Status'),
                'name'      => 'status',
                'required'  => true,
                'class' => 'required-entry',
        ));

        if (Mage::getSingleton("adminhtml/session")->getRmaData())
        {
            $form->setValues(Mage::getSingleton("adminhtml/session")->getRmaData());
            Mage::getSingleton("adminhtml/session")->setRmaData(null);
        } 
        elseif(Mage::registry("rma_data")) {
            $form->setValues(Mage::registry("rma_data")->getData());
        }


    return parent::_prepareForm();

    } 

}
?>