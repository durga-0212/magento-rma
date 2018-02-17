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

    
    public function getshipmentData($shipData=array()) {
          $ordershipcollection = Mage::getModel('sales/order')->getCollection()
                       ->addAttributeToSelect('*')                      
                       ->join(array('sfo' => 'sales/order_item'), 'main_table.entity_id = sfo.order_id', array(
                    'sfo.*'))
                       ->addAttributeToSelect('*')
                       ->join(array('sfg' => 'sales/order_grid'), 'sfo.item_id = sfg.entity_id', array(
                    'sfg.shipping_name'))                     
                       ->addAttributeToFilter('sfo.order_id', $shipData['order_id'])
                       ->addAttributeToFilter('sfo.item_id', $shipData['item_id']);
          return $ordershipcollection;           
    }
    
    public function getShipmentDetails($shipData=array())
    {
        $shipmentCollection = Mage::getResourceModel('sales/order_shipment_collection')
                       ->addAttributeToSelect('entity_id')
                       ->addAttributeToSelect('order_id')
                       ->join(array('sfo' => 'sales/shipment_item'), 'main_table.entity_id = sfo.parent_id', array(
                       'sfo.order_item_id'))
                      ->addAttributeToFilter('main_table.order_id', $shipData['order_id'])
                       ->addAttributeToFilter('sfo.order_item_id', $shipData['item_id']);
        return $shipmentCollection->getData();        
    }    

    public function getRmaOrders() 
    {
        $rmaOrders = array();
        $rmaOrdersCollection = Mage::getModel('rma/order')->getCollection()
        ->join(array('roi' => 'rma/rma_item'), 'main_table.entity_id = roi.rma_entity_id',array('count(roi.rma_entity_id) as itemcount'))
        ->addFieldToSelect('order_id')
        ->addFieldToFilter('customer_id', Mage::getSingleton('customer/session')->getCustomer()->getId());
        $rmaOrdersCollection->getSelect()
        ->group('order_id');
        $rmaOrdersData = $rmaOrdersCollection->getData();
        if(empty($rmaOrdersData))
        {
            return $rmaOrders;
        }
        $finalData = array();
        foreach($rmaOrdersData as $key=>$order)
        {
            $finalData[$key]['order_id'] = $order['order_id'];
            $finalData[$key]['itemcount'] = $order['itemcount'];
        }
        return $finalData;
    }

}
