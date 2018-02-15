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
                       ->addAttributeToSelect('*')
                       ->join(array('sfo' => 'sales/order'), 'main_table.order_id = sfo.entity_id')
                       ->addAttributeToSelect('*')
                       ->addAttributeToFilter('order_id',$id)
                       ->getData();
        return ($productInfo);
    }

     public function getRmaProductsByOrderItemId($orderItemId)
    {
        $rmaProductStatus = array();
        if(empty($orderItemId))
        {
            return $rmaProductStatus;
        }
        $rmaProductStatus = Mage::getModel('rma/order')->getCollection()
                       ->addFieldToSelect('entity_id')
                       ->join(array('roi' => 'rma/rma_item'), 'main_table.entity_id = roi.rma_entity_id',array('roi.item_status','roi.product_name'))
                       ->addFieldToFilter('roi.order_item_id',$orderItemId)
                       ->getData();
        return $rmaProductStatus;
    }
    
    public function getAllRmas() {
         $returns=Mage::getResourceModel('rma/order_collection')
                  ->join(array('sfo' => 'sales/order'), 'main_table.order_id = sfo.entity_id', array(
                    'grand_total'))                                                   
                    ->addFieldToFilter('customer_id', Mage::getSingleton('customer/session')->getCustomer()->getId())                
                ->setOrder('date_requested','desc'); 
         return $returns;         
    }
}
