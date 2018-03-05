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
        $rma_id = $this->getRequest()->getParam('rma_id'); 
        $returns = Mage::getModel('rma/order')->load($rma_id);
        $this->setReturns($returns);
        
        $order = Mage::getModel('sales/order')->load($returns->order_id);       
        if ($order->getId()) {
            $this->setOrdersinfo($order);           
        }  
        
        $modelAttribute = Mage::getModel('rma/rma_attributes');
        $products = $modelAttribute->getAttributesCollection($rma_id);
        
        $modelHistory = Mage::getModel('rma/rma_history');  
        $history = $modelHistory->getHistoryCollection($rma_id);
        $this->setItems($products);
        $this->setRmaHistory($history);
    }
    
    public function getOrderUrl($rma)
    {
        return $this->getUrl('sales/order/view/', array('order_id' => $rma->getOrderId()));
    }
}
