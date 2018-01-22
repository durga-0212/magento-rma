<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class Test_Rma_Adminhtml_RmaController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->_title($this->__("RMA"));
        $this->_title($this->__("RMA Detail"));
        $this->loadLayout();
        $this->renderLayout();
        Zend_Debug::dump($this->getLayout()->getUpdate()->getHandles());
    }
}


