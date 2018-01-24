<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class Test_Rma_Model_Order extends Mage_Core_Model_Abstract
{
    protected function _construct() 
    {
        $this->_init('rma/order');
    }
    
    public function getOrdersById()
    {
        $customerId = Mage::getSingleton('customer/session')->getCustomer()->getId();
        $orderInfo = Mage::getResourceModel("sales/order_collection")
                     ->addFieldToFilter('customer_id',Mage::getSingleton('customer/session')->getCustomer()->getId());
        return $orderInfo;
    }
    
    public function getProductsById($id)
    {
        $productInfo = Mage::getResourceModel('sales/order_item');
        print_r($productInfo);die;
        return ($productInfo->getData());
    }
}
