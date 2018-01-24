<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class Thycart_Rma_Adminhtml_RmaController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->_title($this->__("RMA"));
        $this->_title($this->__("RMA Detail"));
        $this->loadLayout();
        $this->renderLayout();
        //Zend_Debug::dump($this->getLayout()->getUpdate()->getHandles());
    }
    
    public function massRemoveAction()
    {
        try 
        {
            $ids = $this->getRequest()->getPost('id', array());
            foreach ($ids as $id) 
            {
                $model = Mage::getModel("rma/order");
                $model->setId($id)->delete();
            }
                
            Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("RMA(s) was successfully removed"));
        }
        catch (Exception $e) 
        {
            Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
        }
        $this->_redirect('*/*/');
    }
    
    public function exportCsvAction()
    {
        $fileName = 'thycart_rma.csv';
        $grid = $this->getLayout()->createBlock('rma/adminhtml_rma_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }
 
    public function exportExcelAction()
    {
        $fileName = 'thycart_rma.xml';
        $grid = $this->getLayout()->createBlock('rma/adminhtml_rma_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
    }
    
    public function editAction()
    {		

        $this->_title($this->__("RMA"));
        $this->_title($this->__("RMA Detail"));
        $this->_title($this->__("Edit RMA"));

            $id = $this->getRequest()->getParam("id");
            $model = Mage::getModel("rma/order")->load($id);
            if ($model->getId() || $id == 0) {
                    Mage::register("rma_data", $model);

                    $this->loadLayout();
                    $this->_setActiveMenu("sales/rma");
                    $this->_addBreadcrumb(Mage::helper("adminhtml")->__("Rma"), Mage::helper("adminhtml")->__("Rma"));


                    $this->_addContent($this->getLayout()->createBlock("rma/adminhtml_rma_edit"))->_addLeft($this->getLayout()->createBlock("rma/adminhtml_rma_edit_tabs"));
                    $this->renderLayout();
             } 
            else {
                    Mage::getSingleton("adminhtml/session")->addError(Mage::helper("rma")->__("RMA does not exist."));
                    $this->_redirect("*/*/");
            }
    }


    public function newAction()
    {
            $this->_forward('edit');
    }
    
    
}