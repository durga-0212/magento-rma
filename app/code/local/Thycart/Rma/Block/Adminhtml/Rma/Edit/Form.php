<?php
class Thycart_Rma_Block_Adminhtml_Rma_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array(
        "id" => "edit_form",
        "action" => $this->getUrl("*/*/save", array("id" => $this->getRequest()->getParam("id"))),
        "method" => "post",
        "enctype" =>"multipart/form-data",
        )
        );
         $model = Mage::registry('rma_data');

        if ($model) {
            if ($model->getId()) {
                $form->addField('entity_id', 'hidden', array(
                    'name' => 'entity_id',
                ));
                $form->setValues($model->getData());
            }

            $this->_order = ($model->getOrderId());
        }
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
?>
