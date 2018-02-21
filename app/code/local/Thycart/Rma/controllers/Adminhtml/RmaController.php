<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Thycart_Rma_Adminhtml_RmaController extends Mage_Adminhtml_Controller_Action {

    public function indexAction() {
        $this->_title($this->__("RMA Grid"));
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
                $model = Mage::getModel("rma/order")->load($id);
                $model->addData(array("status"=>Thycart_Rma_Model_Rma_Status::STATE_CLOSED));                
                $model->save();
                $modelitem = Mage::getModel("rma/rma_item")->getCollection()
                    ->addFieldToFilter('rma_entity_id',$id);
                    
                foreach($modelitem->getData() as $key=> $value)
                {
                    $modelStatus = Mage::getModel("rma/rma_item")->load($value['entity_id']);
                    $modelStatus->addData(array("item_status"=>Thycart_Rma_Model_Rma_Status::STATE_COMPLETE));                
                    $modelStatus->save();  
                }                              
            }
            Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("RMA(s) was successfully removed"));
        } 
        catch (Exception $e) 
        {
            Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
        }
        $this->_redirect('*/*/');
    }
    
    public function massRemoveItemAction() 
    {      
        try 
        {           
            $ids = $this->getRequest()->getPost('id', array());           
            foreach ($ids as $id) 
            {
                $model = Mage::getModel("rma/rma_item")->load($id);
                $model->addData(array("item_status"=>Thycart_Rma_Model_Rma_Status::STATE_COMPLETE));               
                $model->save();   
                foreach($model->getData() as $key=> $value)
                {
                    $modelStatus = Mage::getModel("rma/order")->load($value['rma_entity_id']);
                    $modelStatus->addData(array("status"=>Thycart_Rma_Model_Rma_Status::STATE_CLOSED));                
                    $modelStatus->save();  
                    break;
                }                
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
        if ($model->getId() || $id == 0) 
        {
            Mage::register("rma_data", $model);
            $this->loadLayout();
            $this->_setActiveMenu("sales/rma");
            $this->renderLayout();
        } 
        else 
        {
            Mage::getSingleton("adminhtml/session")->addError(Mage::helper("rma")->__("RMA does not exist."));
            $this->_redirect("*/*/");
        }
    }

    public function newAction() 
    {
        $this->_forward('edit');
    }

    public function productGridAction() 
    {
        $this->loadLayout();
        $this->getLayout()->getBlock('adminhtml.rma.edit.tab.productgrid');
        $this->renderLayout();
    }

    public function addCommentAction() 
    {
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
        $post_data = $this->getRequest()->getPost(); 
        $id = $this->getRequest()->getParam('id');
        $rmaItemArray = array();
        $sendLink = 0;
        $modelRma = Mage::getModel('rma/order')->load($id);
        $customerId = $modelRma->getCustomerId();
        if($post_data)
        {
            $counter=0; 
            try 
            {
                foreach ($post_data['items'] as $key => $value) 
                {
                    $statusModel = Mage::getModel('rma/rma_item')->load($key);
                    $status = $statusModel->getItemStatus();

                    $model = Mage::getModel("rma/rma_item")->load($key);
                    $model->addData(array("item_status" => $value['status'],"qty_approved" => $value['qty_approved']));
                    $result = $model->save();

                    $processing_status=Thycart_Rma_Model_Rma_Status::STATE_PROCESSING;
                    $return_received_status=Thycart_Rma_Model_Rma_Status::STATE_RETURN_RECEIVED;               
                    if($value['status'] == $processing_status && $status!=$processing_status)
                    {
                        $saveShipmentNumber = $this->saveShipmentNumber($post_data['order_id'],$value);
                    }
                    elseif($value['status'] == $return_received_status && $status!= $return_received_status)
                    {
                        $updateInventory =$this->updateInventory($value);
                        $rmaItemArray[] = $key;
                        $sendLink = 1;                      
                    }

                    $arr=array(Thycart_Rma_Model_Rma_Status::STATE_COMPLETE,Thycart_Rma_Model_Rma_Status::STATE_CANCELED);

                    if(in_array($value['status'], $arr))
                    {               
                        $counter++;              
                    }  
                }
                if($updateInventory)
                {
                    Mage::getSingleton('core/session')->addSuccess('Inventory Updated');
                }
                if($saveShipmentNumber)
                {
                    Mage::getSingleton('core/session')->addSuccess('Tracking Number is generated');
                }
                if($sendLink)
                {
                    $this->saveRmaLink($id,$rmaItemArray,$customerId);
                }
                $modelRma->addData(array('status'=>Thycart_Rma_Model_Rma_Status::STATE_PENDING));

                if(count($post_data['items']) === $counter)
                {                    
                    $modelRma->addData(array('status'=>Thycart_Rma_Model_Rma_Status::STATE_CLOSED));
                }
                $modelRma->save();
            }
            catch(Exception $e)
            {
                Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
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
    
    public function saveShipmentNumber($order_id,$value)
    {
        $shipData=array(
            'order_id'=> $order_id,
            'item_id' => $value['order_item_id']
        );
        $shipDetails = Mage::getModel('rma/order')->getShipmentDetails($shipData);
        $track_data= Mage::helper('rma')->getTrackingNumber();                   
        $track_details= explode('_', $track_data); 
        foreach ($shipDetails as $key => $value)               
        {                    
            $shipmenttrackModel = Mage::getModel('sales/order_shipment_track');
            $shipmenttrackModel->addData(array(
                'parent_id'  => $value['entity_id'],
                'order_id' => $value['order_id'],
                'track_number'  => $track_details[2],                        
                'title' => $track_details[0],
                'carrier_code' =>  $track_details[1],
                'created_at' =>Mage::getModel('core/date')->gmtDate('Y-m-d H:i:s')                                         
            ));

        $successShipment = $shipmenttrackModel->save(); 
        }
        return $successShipment;                 
    }
    
    public function updateInventory($value)
    {
        $modelSalesItem = Mage::getModel('sales/order_item')->load($value['order_item_id']);
        $pid = $modelSalesItem->getProductId();
        $inventoryModel = Mage::getModel('cataloginventory/stock_item')->load($pid);
        $backOrders = $inventoryModel->getBackorders();
        $originalQty = $inventoryModel->getQty();
        $qty = $value['qty_approved'];
        $updatedQty = $originalQty+$qty;
        if($backOrders == 0 || $originalQty>0)
        {
            $inventoryModel->addData(array('qty'=>$updatedQty));
            $successInventory = $inventoryModel->save();
            return $successInventory;
        }
    }
    
    public function saveRmaLink($rmaOrderId,$rmaItemIdArray,$customerId)
    {   
        if(empty($rmaOrderId) || empty($rmaItemIdArray) || empty($customerId))
        {
            Mage::getSingleton('core/session')->addError('Invalid data while saving Rma Link details');
        }
        else 
        {
            foreach($rmaItemIdArray as $key=>$rmaItemId)
            {
                $modelLink = Mage::getModel('rma/link');
                $modelLink->addData(array(
                    'rma_order_id'=>$rmaOrderId,
                    'rma_order_item_id'=>$rmaItemId,
                    'customer_id'=>$customerId,
                    'status'=>0
                ));
                $modelLink->save();
            }
            $link = "http://127.0.0.1/magento-rma/index.php/rma/index/bankform/rmaItemId/".implode("-",$rmaItemIdArray);
            $from = 'anjalee.singh@adapty.com';
            $to = 'anjalee.singh@adapty.com';
            $subject = 'Return Received';
            $body = 'Your product has been received';
            $this->sendMail($from,$to,$subject,$body,$link);
        }
    }

    public function sendMail($from,$to,$subject,$body,$link='')
    {
        Mage::helper('rma')->sendMail($from,$to,$subject,$body,$link);
    }
    
}
