<?php
class Thycart_Rma_Block_Adminhtml_Customer_Tab
extends Mage_Adminhtml_Block_Template
implements Mage_Adminhtml_Block_Widget_Tab_Interface {
   /**
     * Set the template for the block
     *
     */
    public function _construct()
    {
        parent::_construct();
       $this->setTemplate('rma/customer/tab.phtml');       
    }
   /**
     * Retrieve the label used for the tab relating to this block
     *
     * @return string
     */
    public function getTabLabel()
    {
        return $this->__('Bank Details');
    }
   /**
     * Retrieve the title used by this tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->__('Click here to view your custom tab content');
    }
   /**
     * Determines whether to display the tab
     * Add logic here to decide whether you want the tab to display
     *
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }
    /**
     * Stops the tab being hidden
     *
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }
    
    public function getCustomerModel()
    {
        if($this->getRequest()->getParam("id"))
        {
            $id = $this->getRequest()->getParam("id");
        }
        else 
        {
            $id = Mage::getModel('customer/session')->getCustomer()->getId();
        }
        $model = Mage::getModel('customer/customer')->load($id);
        return $model;
    }
    
    public function getBankName()
    {
        $customerModel = $this->getCustomerModel();
        $name = $customerModel->getBankname();
        return $name;
    }
    
    public function getAccountNumber() 
    {
        $customerModel = $this->getCustomerModel();
        $accountNumber = $customerModel->getAccountNo();
        return $accountNumber;
    }
    
    public function getIfscCode() 
    {
        $customerModel = $this->getCustomerModel();
        $ifscCode = $customerModel->getIfscCode();
        return $ifscCode;
    }
    
}