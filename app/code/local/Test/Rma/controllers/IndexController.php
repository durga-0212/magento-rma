<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class Test_Rma_IndexController extends Mage_Core_Controller_Front_Action
{
    
      /**
     * Action predispatch
     *
     * Check customer authentication for some actions
     */
    public function preDispatch()
    {
        parent::preDispatch();
        $action = $this->getRequest()->getActionName();
        $loginUrl = Mage::helper('customer')->getLoginUrl();

        if (!Mage::getSingleton('customer/session')->authenticate($this, $loginUrl)) {
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
        }
    }
    
    
     /**
     * Customer order history
     */
    public function indexAction()
    {
         $this->loadLayout();
         $this->_initLayoutMessages('catalog/session');
         $this->getLayout()->getBlock('head')->setTitle($this->__('My Rma Returns History'));
         $this->renderLayout();
      // $result = Mage::getStoreConfig('rma_section/rma_group/rma_field');
      // echo $result;
       
    }
}
