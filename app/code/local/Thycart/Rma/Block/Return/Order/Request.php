<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class Thycart_Rma_Block_Return_Order_Request extends Mage_Core_Block_Template
{
    public function __construct() 
    {
        $this->setTemplate('rma/return/request.phtml');    
    }
    
    public function getOrders()
    {  
        $orderInfo = Mage::getModel('rma/order')->getOrdersById();       
        return $orderInfo;
    }
     
    public function getPid($data)
    {
       return Mage::register('productInfo', $data);
    }
    
    public function getRmaOrder()
    {
        $collection = Mage::getModel('rma/rma_item')->getCollection();
        return $collection; 
    }
}

