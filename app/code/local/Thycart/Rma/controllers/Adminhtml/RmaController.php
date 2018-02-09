<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Thycart_Rma_Adminhtml_RmaController extends Mage_Adminhtml_Controller_Action {

    public function indexAction() {
        $this->_title($this->__("RMA"));
        $this->_title($this->__("RMA Detail"));
        $this->loadLayout();
        $this->renderLayout();
        //Zend_Debug::dump($this->getLayout()->getUpdate()->getHandles());
    }

    public function massRemoveAction() {
        try {
            $ids = $this->getRequest()->getPost('id', array());
            foreach ($ids as $id) {
                $model = Mage::getModel("rma/order");
                $model->setId($id)->delete();
            }

            Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("RMA(s) was successfully removed"));
        } catch (Exception $e) {
            Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
        }
        $this->_redirect('*/*/');
    }

    public function exportCsvAction() {
        $fileName = 'thycart_rma.csv';
        $grid = $this->getLayout()->createBlock('rma/adminhtml_rma_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }

    public function exportExcelAction() {
        $fileName = 'thycart_rma.xml';
        $grid = $this->getLayout()->createBlock('rma/adminhtml_rma_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
    }

    public function editAction() {

        $this->_title($this->__("RMA"));
        $id = $this->getRequest()->getParam("id");
        $model = Mage::getModel("rma/order")->load($id);
        if ($model->getId() || $id == 0) {
            Mage::register("rma_data", $model);
            $this->loadLayout();
            $this->_setActiveMenu("sales/rma");
            $this->_addBreadcrumb(Mage::helper("adminhtml")->__("Rma"), Mage::helper("adminhtml")->__("Rma"));
            $this->renderLayout();
            //Zend_Debug::dump($this->getLayout()->getUpdate()->getHandles());
        } else {
            Mage::getSingleton("adminhtml/session")->addError(Mage::helper("rma")->__("RMA does not exist."));
            $this->_redirect("*/*/");
        }
    }

    public function newAction() {
        $this->_forward('edit');
    }

    public function productGridAction() {
        $this->loadLayout();
        $this->getLayout()->getBlock('adminhtml.rma.edit.tab.productgrid');
        $this->renderLayout();
    }

    public function addCommentAction() {
        try {
            $model = Mage::getModel('rma/order');
            $data = $this->getRequest()->getPost('comment');
            $rmaId = isset($data['rma_id']) ? $data['rma_id'] : '';
            if ($rmaId) {
                $model->load($rmaId);
                if (!$model->getId()) {
                    Mage::throwException($this->__('Wrong RMA requested.'));
                }
                Mage::register('rma_data', $model);
            }
            $notify = isset($data['is_customer_notified']) ? $data['is_customer_notified'] : false;
            $visible = isset($data['is_visible_on_front']) ? $data['is_visible_on_front'] : false;

            $status = isset($data['status']) ? $data['status'] : '';
            $comment = trim($data['comment']);
            if (!$comment) {
                Mage::throwException(Mage::helper('rma')->__('Enter valid message.'));
            }

            $history = Mage::getModel('rma/rma_history');
            $history->setRmaEntityId((int) $rmaId)
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
                'error' => true,
                'message' => $e->getMessage(),
            );
        } catch (Exception $e) {
            $response = array(
                'error' => true,
                'message' => $this->__('Cannot add RMA history.'),
            );
        }
        if (is_array($response)) {
            $response = Mage::helper('core')->jsonEncode($response);
        }
        $this->getResponse()->setBody($response);
    }

    public function saveAction() {
        $post_data = $this->getRequest()->getPost('items');  
        $id = $this->getRequest()->getParam('id');
        if($post_data)
        {
            $flag=0;
            try{
            foreach ($post_data as $key => $value) {           
                $model = Mage::getModel("rma/rma_item")->load($key);
                $model->addData(array("item_status" => $value['status'], "resolution" => $value['resolution']));
                $result = $model->save();
                 
                $arr=array('approved','rejected');
                $arr1=array('pending','processing');           
    //           $arr3=array('approved','processing');
                if(in_array($value['status'], $arr))
                {               
                    $flag=1;              
                }
                elseif(in_array($value['status'], $arr1))
                {
                     $flag=2; 
    //                if(in_array($value['status'], $arr3))
    //                {
    //                    $flag=3;
    //                }                
                }          
            } 

            $modelRma = Mage::getModel('rma/order')->load($id);
            if($flag == 1)
            {                    
                $modelRma->addData(array('status'=>'closed'));
            }
            else 
            {
                $modelRma->addData(array('status'=>'pending'));
            }
            $modelRma->save();
            }
            catch(Exception $e){
                Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
                Mage::getSingleton("adminhtml/session")->setMessageData($this->getRequest()->getPost());
                $this->_redirect("*/*/edit", array("id" => $this->getRequest()->getParam("id")));
                return;
            }
            
        }
        else 
        {
            Mage::getSingleton('core/session')->addError('Data not posted');
        }

        $this->_redirect("*/*/");
    }

}
