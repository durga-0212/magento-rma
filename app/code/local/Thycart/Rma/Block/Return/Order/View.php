<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class Thycart_Rma_Block_Return_Order_View extends Mage_Core_Block_Template
{
     public function __construct() {      
        parent::__construct();       
        $this->setTemplate('rma/return/view.phtml'); 
        $order_id=$this->getRequest()->getParam('order_id');
        $order = Mage::getModel('sales/order')->load($order_id);
        if ($order->getId()) {
            $this->setOrdersinfo($order);           
         }       
        $returns=Mage::getModel('rma/order')->load($order_id, 'order_id');
                 $this->setReturns($returns);                
        $products=Mage::getResourceModel('rma/rma_attributes_collection')
                 ->join(array('rfoi' => 'rma/rma_item'), 'main_table.rma_entity_id = rfoi.rma_entity_id', array(
                    '*'))
//                 ->join(array('sfo' => 'sales/order'), 'main_table.increment_id = sfo.increment_id', array(
//                    'grand_total'))                    
                     ->addFieldToFilter('main_table.rma_entity_id',$returns->getId());              
//                ->setOrder('date_requested','desc');        
//                
//                ->load($returns->getId(),'rma_entity_id');                  
        $history=Mage::getResourceModel('rma/rma_history_collection')
                 ->join(array('ro' => 'rma/order'), 'main_table.rma_entity_id = ro.entity_id', array(
                    'customer_name'))
                ->addFieldToFilter('rma_entity_id',$returns->getId());
        $this->setItems($products);
        $this->setRmaHistory($history);            
    }
    
     public function getOrderUrl($rma)
    {
        return $this->getUrl('sales/order/view/', array('order_id' => $rma->getOrderId()));
    }
}
