<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class Thycart_Rma_Block_Return_History extends Mage_Core_Block_Template
{
    public function __construct() {      
        parent::__construct(); 
        $this->setTemplate('rma/return/history.phtml'); 
        Mage::app()->getFrontController()->getAction()->getLayout()->getBlock('root')->setHeaderTitle(Mage::helper('rma')->__('My Returns'));
        $returns=Mage::getModel('rma/order')->getAllRmas();
//                    ->join(array('sfoi' => 'sales/order_item'), 'main_table.order_id = sfoi.order_id', array(
//                    'product_id'))
//                 ->join(array('sfo' => 'sales/order'), 'main_table.increment_id = sfo.increment_id', array(
//                    'grand_total'))                    
//                    ->addFieldToFilter('customer_id', Mage::getSingleton('customer/session')->getCustomer()->getId())                
//                ->setOrder('date_requested','desc');        
        $this->setReturns($returns);               
    }
    
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $pager = $this->getLayout()->createBlock('page/html_pager')
                ->setCurPage(0)
                ->setLimit(1)
            ->setCollection($this->getReturns());
        $this->setChild('pager', $pager);
        $this->getReturns()->load();
        return $this;
    }

    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }
    
    public function getViewUrl($order)
    {
        return $this->getUrl('*/*/view', array('order_id' => $order->getOrderId()));
    }
    
     public function getRequestUrl($path)
    {
        return $this->getUrl('*/*/'.$path);
    }
    

}