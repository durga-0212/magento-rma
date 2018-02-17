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
        $this->setReturns($returns);               
    }
    
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $pager = $this->getLayout()->createBlock('page/html_pager', 'rma.return.history.pager')
                //->setCurPage(0)
                //->setLimit(1)
            ->setCollection($this->getReturns());
        $this->setChild('pager', $pager);
        $this->getReturns()->load();
        return $this;
    }

    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }
    
    public function getViewUrl($rmaId)
    {
        return $this->getUrl('*/*/view', array('rma_id' => $rmaId));
    }
    
     public function getRequestUrl($path)
    {
        return $this->getUrl('*/*/'.$path);
    }
    
    public function getBackUrl()
    {
        // the RefererUrl must be set in appropriate controller
        if ($this->getRefererUrl()) {
            return $this->getRefererUrl();
        }
        return $this->getUrl('rma/index/');
    }

}