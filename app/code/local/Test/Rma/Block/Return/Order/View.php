<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class Test_Rma_Block_Return_Order_View extends Mage_Core_Block_Template
{
     public function __construct() {      
        parent::__construct(); 
        $this->setTemplate('rma/return/order_view.phtml'); 
        
        Mage::app()->getFrontController()->getAction()->getLayout()->getBlock('root')->setHeaderTitle(Mage::helper('rma')->__('My Returns'));
        $returns=Mage::getResourceModel('rma/order_collection')
                    ->join(array('sfoi' => 'sales/order_item'), 'main_table.order_id = sfoi.order_id', array(
                    'product_id'))
                 ->join(array('sfo' => 'sales/order'), 'main_table.increment_id = sfo.increment_id', array(
                    'grand_total'))                    
                    ->addFieldToFilter('customer_id', Mage::getSingleton('customer/session')->getCustomer()->getId())                
                ->setOrder('date_requested','desc');        
        $this->setReturns($returns);               
    }
}
