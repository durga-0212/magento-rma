<?php
class Thycart_Rma_Block_Adminhtml_Rma_Item_Attribute_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array(
        "id" => "edit_form",
        "action" => $this->getUrl("*/*/save", array("id" => $this->getRequest()->getParam("id"))),
        "method" => "post",
        )
        );
        // $model = Mage::registry('attribute_data');

        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
?>
