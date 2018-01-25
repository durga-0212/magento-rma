<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class Thycart_Rma_Model_Order extends Mage_Core_Model_Abstract
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
        $productInfo = Mage::getModel('sales/order_item')->getCollection()
                       ->addAttributeToSelect('name')
                       ->addAttributeToSelect('sku')
                       ->addAttributeToSelect('qty_ordered')
                       ->addAttributeToFilter('order_id',$id)->getData();
        print_r($productInfo);die;
        return ($productInfo);
    }
}
