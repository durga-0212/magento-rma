<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class Test_Rma_Block_Return_History extends Mage_Core_Block_Template
{
    public function __construct() {
        parent::__construct();             
        $returns=Mage::getModel('rma/order')->getCollection()
                ->addFieldToFilter('customer_id', Mage::getSingleton('customer/session')->getCustomer()->getId())
                ->setOrder('date_requested','desc'); 
        $this->setReturns($returns);
        Mage::app()->getFrontController()->getAction()->getLayout()->getBlock('root')->setHeaderTitle(Mage::helper('sales')->__('My Returns'));
        $this->setTemplate('rma/return/history.phtml');  
        
    }
}