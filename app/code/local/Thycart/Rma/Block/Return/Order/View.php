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
        $order_id = $this->getRequest()->getParam('order_id');        
        $order = Mage::getModel('sales/order')->load($order_id);       
        if ($order->getId()) {
            $this->setOrdersinfo($order);           
        }       
        $returns=Mage::getModel('rma/order')->load($order_id, 'order_id');
                 $this->setReturns($returns);  
        
        
        $modelAttribute = Mage::getModel('rma/rma_attributes');
        $products = $modelAttribute->getAttributesCollection($returns->getId());
        
        $modelHistory = Mage::getModel('rma/rma_history');  
        $history = $modelHistory->getHistoryCollection($returns->getId());
        $this->setItems($products);
        $this->setRmaHistory($history);
    }
    
     public function getOrderUrl($rma)
    {
        return $this->getUrl('sales/order/view/', array('order_id' => $rma->getOrderId()));
    }
}
