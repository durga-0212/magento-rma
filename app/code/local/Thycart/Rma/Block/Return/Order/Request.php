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
        $this->setCancelType(0);
        if($this->getRequest()->getActionName() == 'cancelOrder')
        {
            $this->setRmaOrderId($this->getRequest()->getParam('order_id'));
            $this->setCancelType(1);
        }
        $this->setTemplate('rma/return/rma.phtml');    
    }
    
    public function getOrders()
    {  
        $orderInfo = Mage::getModel('rma/order')->getOrdersById();       
        return $orderInfo;
    }

    public function getRmaOrders()
    {
        $rmaOrders = Mage::getModel('rma/order')->getRmaOrders();    
        return $rmaOrders;
    }
     
    public function getPid($data)
    {
       return Mage::register('productInfo', $data);
    }
    
    public function getOrderType()
    {
        $collection = Mage::getModel('rma/rma_item')->getCollection();
        return $collection; 
    }
    
    public function getDays() 
    {   
        $configDays = Mage::getStoreConfig('rma_section/rma_group/rma_days'); 
        $currentDate = Mage::getModel('core/date')->date('Y-m-d H:i:s');
        $rmaOrders = Mage::getResourceModel('sales/order_collection');
        foreach ($rmaOrders as $key => $value)
        {
            $diff = abs(strtotime($currentDate) - strtotime($value['created_at']));
            $years = floor($diff / (365*60*60*24));
            $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
            $days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24) / (60*60*24));
            
            if($days > $configDays)
            {
                return 0;
            }
            else 
            {
                return 1;
            }
        }
        
    }
}

