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
        $id = $this->getRequest()->getParam("id");
            $model = Mage::getModel("rma/order")->load($id);
            if ($model->getId() || $id == 0) {
               Mage::register("rma_data", $model);
                    $this->loadLayout();
                    $this->_setActiveMenu("sales/rma");
                    $this->_addBreadcrumb(Mage::helper("adminhtml")->__("Rma"), Mage::helper("adminhtml")->__("Rma"));

                   
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
    
    public function productGridAction() {       
       $this->loadLayout();
      $this->getLayout()->getBlock('adminhtml.rma.edit.tab.productgrid'); 
     $this->renderLayout();        
    }
    
    public function editRmaAction() {
        echo 'tesxddx';
         $this->loadLayout();
        //$this->getLayout()->createBlock("rma/adminhtml_rma_edit_tab_form")->toHtml();  
        $this->renderLayout();
        }
        
        public function saveAction() {
            //echo 'test'; die;
        }
        
      protected function _initModel($requestParam = 'id')
    {
        $model = Mage::getModel('rma/order');
        $model->setStoreId($this->getRequest()->getParam('store', 0));

        $rmaId = $this->getRequest()->getParam();
        echo $rmaId; die;
        if ($rmaId) {
            $model->load($rmaId);
            if (!$model->getId()) {
                Mage::throwException($this->__('Wrong RMA requested.'));
            }
            Mage::register('rma_data', $model);
            $orderId = $model->getOrderId();
        } else {
            $orderId = $this->getRequest()->getParam('order_id');
        }

        if ($orderId) {
            $order = Mage::getModel('sales/order')->load($orderId);
            if (!$order->getId()) {
                Mage::throwException($this->__('Wrong RMA order id.'));
            }
            Mage::register('current_order', $order);
        }

        return $model;
    }

    
    public function addCommentAction()
    {
         try {
        $model = Mage::getModel('rma/order');
        $data = $this->getRequest()->getPost('comment');
        $rmaId=isset($data['rma_id'])? $data['rma_id']:'';  
        if ($rmaId) {
            $model->load($rmaId);
            if (!$model->getId()) {
                Mage::throwException($this->__('Wrong RMA requested.'));
            }
            Mage::register('rma_data', $model);          
        }
            $notify = isset($data['is_customer_notified']) ? $data['is_customer_notified'] : false;
            $visible = isset($data['is_visible_on_front']) ? $data['is_visible_on_front'] : false;
                      
            $status=isset($data['status'])? $data['status']:'';
            $comment = trim($data['comment']);
            if (!$comment) {
                Mage::throwException(Mage::helper('rma')->__('Enter valid message.'));
            }

            $history = Mage::getModel('rma/rma_history');
            $history->setRmaEntityId((int)$rmaId)
                ->setComment($comment)
                ->setIsVisibleOnFront($visible)
                ->setIsCustomerNotified($notify)
                ->setStatus($status)
                ->setCreatedAt(Mage::getSingleton('core/date')->gmtDate())
                ->setIsAdmin(1)        
                ->save();

      
            $this->loadLayout();
            $response = $this->getLayout()->getBlock('comments_history')->toHtml();
           } catch (Mage_Core_Exception $e) {
            $response = array(
                'error'     => true,
                'message'   => $e->getMessage(),
            );
        } catch (Exception $e) {
            $response = array(
                'error'     => true,
                'message'   => $this->__('Cannot add RMA history.'),
            );
        }   
        if (is_array($response)) {
            $response = Mage::helper('core')->jsonEncode($response);
        }        
        $this->getResponse()->setBody($response);
    }
    
}