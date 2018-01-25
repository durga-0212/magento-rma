<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class Thycart_Rma_IndexController extends Mage_Core_Controller_Front_Action
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
    
    public function addrequestAction() 
    {
        $this->loadLayout();
        $this->renderLayout();
    }
    
    
    public function viewAction()
    {
         $this->loadLayout();        
        // $this->_initLayoutMessages('catalog/session');
        // $this->getLayout()->getBlock('head')->setTitle($this->__('My Rma Returns History'));
         $this->renderLayout();
         // Zend_Debug::dump($this->getLayout()->getUpdate()->getHandles());
    }
    
    public function saveCommentAction() {
       $postData= $this->getRequest()->getParams();
       $postData['created_at']=Mage::getModel('core/date')->gmtDate('Y-m-d H:i:s');      
       $modelObj=Mage::getModel('rma/rma_history')->setData($postData)->save();
       if($modelObj)
       {
        $url = Mage::helper('core/http')->getHttpReferer() ? Mage::helper('core/http')->getHttpReferer():$this->_getRefererUrl();
               Mage::app()->getResponse()->setRedirect($url);  
       }
    }
    
    public function productinfoAction()
    {
        $data = $this->getRequest()->getParam('OrderId');
        $productInfo = Mage::getModel('rma/order')->getProductsById($data);
        print_r($productInfo);die;
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($productInfo));
    }
}
