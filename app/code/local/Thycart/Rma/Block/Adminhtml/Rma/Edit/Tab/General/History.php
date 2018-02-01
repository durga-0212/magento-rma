<?php

class Thycart_Rma_Block_Adminhtml_Rma_Edit_Tab_General_History
    extends Mage_Core_Block_Template
{
    protected function _prepareLayout()
    {
        $onclick = "submitAndReloadArea($('rma-history-block').parentNode, '".$this->getSubmitUrl()."')";
        $button = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(array(
                'label'   => Mage::helper('rma')->__('Submit Comment'),
                'class'   => 'save',
                'onclick' => $onclick
            ));
        $this->setChild('submit_button', $button);

        return parent::_prepareLayout();
    }
//
//    /**
//     * Get config value - is Enabled RMA Comments Email
//     *
//     * @return bool
//     */
//    public function canSendCommentEmail()
//    {       
//        $configRmaEmail = Mage::getSingleton('rma/config');
//        $configRmaEmail->init($configRmaEmail->getRootCommentEmail(), $this->getOrder()->getStore());
//        return $configRmaEmail->isEnabled();
//    }
//
//    /**
//     * Get config value - is Enabled RMA Email
//     *
//     * @return bool
//     */
//    public function canSendConfirmationEmail()
//    {
//        /** @var $configRmaEmail Enterprise_Rma_Model_Config */
//        $configRmaEmail = Mage::getSingleton('rma/config');
//        $configRmaEmail->init($configRmaEmail->getRootRmaEmail(), $this->getOrder()->getStore());
//        return $configRmaEmail->isEnabled();
//    }
//
//    /**
//     * Get URL to add comment action
//     *
//     * @return string
//     */
    public function getSubmitUrl()
    {
        return $this->getUrl('*/*/addComment', array('id'=>$this->getRmaData('entity_id')));
    }
//
    public function getComments() { 
        $history=Mage::getResourceModel('rma/rma_history_collection')
                 ->join(array('ro' => 'rma/order'), 'main_table.rma_entity_id = ro.entity_id', array(
                    'customer_name'))
                ->addFieldToFilter('rma_entity_id',Mage::registry('rma_data')->getEntityId());
        return $history;
    }
}
