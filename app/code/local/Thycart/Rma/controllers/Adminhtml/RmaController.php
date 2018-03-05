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
        $this->_title($this->__("RMA Grid"));
        $this->loadLayout();
        $this->_setActiveMenu("sales/rma");
        $this->renderLayout();
    }

    public function massRemoveAction() 
    {    
        if(empty($this->getRequest()->getPost('id')))
        {
            return;
        }
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
            Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("RMA(s) was successfully Closed"));
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
        try
        {
            $this->_title($this->__("RMA Description"));
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
        catch(Exception $e)
        {
            Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
            $this->_redirect("*/*/edit", array("id" => $this->getRequest()->getParam("id")));
            return;
        }
    }

    public function newAction() 
    {
        $this->_forward('edit');
    }

    public function productGridAction() 
    {
        try
        {
            $this->loadLayout();
            $this->getLayout()->getBlock('adminhtml.rma.edit.tab.productgrid');
            $this->renderLayout();
        }
        catch(Exception $e)
        {
            Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
            $this->_redirect("*/*/edit", array("id" => $this->getRequest()->getParam("id")));
            return;
        }
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

    public function saveAction() 
    {            
        if(empty($this->getRequest()->getParam('order_id')) || empty($this->getRequest()->getParam('items')))
        {
            Mage::getSingleton('core/session')->addError('Please fill all the details');
            $this->_redirect('*/*/edit',array("id" => $this->getRequest()->getParam("id")));
            return;
        }
        $rmaItemArray = array();
        $productArray = array();
        $updateInventory = '';
        $saveShipmentNumber = '';
        $sendLink = 0;
        $completeMail = 0;
        $counter = 0;
        $id = $this->getRequest()->getParam('id');
        $post_data = $this->getRequest()->getPost();
        $orderId = $post_data['order_id'];
        $modelRma = Mage::getModel('rma/order')->load($id);
        $customerId = $modelRma->getCustomerId();        
        $statusCheckArray = array_column($post_data['items'],'status');
        $qtyApprovedArray = array_column($post_data['items'],'qty_approved');        
        
        if(in_array(Thycart_Rma_Model_Rma_Status::STATE_PENDING,$statusCheckArray))
        {
            Mage::getSingleton('adminhtml/session')->addError("Select Processing for all items");
            $this->_redirect('*/*/edit',array("id" => $this->getRequest()->getParam("id")));
            return;
        }
        
        $statusResult = $this->checkForQuantity($post_data['items']);
        if($statusResult)
        {
            Mage::getSingleton('core/session')->addError('Approved Quantity should be greater than zero and less than or equal quantity requested');
            $this->_redirect('*/*/edit',array("id" => $this->getRequest()->getParam("id")));
            return;
        }

        try 
        {   
            foreach ($post_data['items'] as $key => $value) 
            {  
                if(empty($value['status']))
                {
                    Mage::getSingleton('core/session')->addError('Please fill all the details');
                    $this->_redirect('*/*/edit',array("id" => $this->getRequest()->getParam("id")));
                    return;
                }
                
                $flag = 0;
                $rmaItemModel = Mage::getModel('rma/rma_item')->load($key);
                $status = $rmaItemModel->getItemStatus();
                $productName = $rmaItemModel->getProductName();
                $productId = $rmaItemModel->getProductId(); 
                $orderItemId = $rmaItemModel->getOrderItemId();                
                $qtyRequested = $rmaItemModel->getQtyRequested();
                $qtyApproved = $rmaItemModel->getQtyApproved();
                
                $processing_status=Thycart_Rma_Model_Rma_Status::STATE_PROCESSING;
                $return_received_status=Thycart_Rma_Model_Rma_Status::STATE_RETURN_RECEIVED;

                if($value['status'] == $processing_status && $status!= $processing_status && $value['qty_approved']>0
                    && $value['qty_approved']<=$qtyRequested)
                {
                    $saveShipmentNumber = $this->saveShipmentNumber($post_data['order_id'],$orderItemId);
                    $flag = 1;
                }
                elseif($value['status'] == $return_received_status && $status!= $return_received_status && $value['qty_approved']>0
                    && $value['qty_approved'] == $qtyApproved)
                {
                    $updateInventory = Mage::helper('rma')->updateInventory($productId,$value['qty_approved']);
                    $modelProductName = Mage::getModel('rma/rma_item')->load($productId,'product_id');
                    $name = $modelProductName->getProductName();
                    $returnProductArray[$name] = $value['qty_approved'];
                    $rmaItemArray[] = $key;
                    $sendLink = 1; 
                    $flag = 1;
                }
                elseif($value['status'] == Thycart_Rma_Model_Rma_Status::STATE_COMPLETE && $status!= Thycart_Rma_Model_Rma_Status::STATE_COMPLETE 
                    && $value['qty_approved']>0 && $value['qty_approved'] == $qtyApproved)
                {
                    $completeMail = 1;
                    $flag = 1;
                }
                elseif($value['status'] == Thycart_Rma_Model_Rma_Status::STATE_CANCELED && $status!=Thycart_Rma_Model_Rma_Status::STATE_CANCELED 
                    && $value['qty_approved']>0 && $value['qty_approved'] == $qtyApproved)
                {
                    $flag = 1;
                }
                elseif($value['qty_approved']>0 && $value['qty_approved'] == $qtyApproved)
                {
                    $flag = 1;
                }
                else 
                {
                    Mage::getSingleton('adminhtml/session')->addError('Enter valid approved quantity');
                    $this->_redirect('*/*/edit',array("id" => $this->getRequest()->getParam("id")));
                    return;
                }
                
                if($flag)
                {
                    $rmaItemModel->addData(array("item_status" => $value['status'],"qty_approved" => $value['qty_approved']));                
                    $result = $rmaItemModel->save();
                }
                
                $arr = array(Thycart_Rma_Model_Rma_Status::STATE_COMPLETE,Thycart_Rma_Model_Rma_Status::STATE_CANCELED);
                if(in_array($value['status'], $arr))
                {               
                    $counter++;              
                }
                $productArray[$productName] = $value['qty_approved'];                 
            }
            
            if($updateInventory)
            {
                Mage::getSingleton('core/session')->addSuccess('Inventory Updated');
            }
            if($saveShipmentNumber)
            {
                Mage::getSingleton('core/session')->addSuccess('Tracking Number is generated');
                $subject = 'RMA Processed for OrderId '.$orderId;
                $message = 'Rma Request in Processing State';
                $resultMail = Mage::helper('rma')->sendMail($modelRma->getCustomerEmail(),$modelRma->getCustomerName(),$subject,$orderId,$productArray,$message);
            }
            if($sendLink)
            {
                $this->saveRmaLink($id,$rmaItemArray,$customerId,$modelRma->getCustomerEmail(),$modelRma->getCustomerName(),$orderId,$returnProductArray);
            }
            if($completeMail)
            {
                $subject = 'RMA Completed of OrderId '.$orderId;
                $message = 'Rma Request Completed';
                $resultMail = Mage::helper('rma')->sendMail($modelRma->getCustomerEmail(),$modelRma->getCustomerName(),$subject,$orderId,$productArray,$message);
                Mage::getSingleton('core/session')->addSuccess('Rma Request has been completed');
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

        $this->_redirect("*/*/");
    }
    
    public function saveShipmentNumber($order_id,$orderItemId)
    {
        if(empty($order_id) || empty($orderItemId))
        {
            return;
        }
        $shipData=array(
            'order_id'=> $order_id,
            'item_id' => $orderItemId
        );
        try
        {
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
        catch(Exception $e)
        {
            Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
            return;
        }
    }
    
    public function saveRmaLink($rmaOrderId,$rmaItemIdArray,$customerId,$customerEmail,$customerName,$orderId,$productArray)
    {   
        if(empty($rmaOrderId) || empty($rmaItemIdArray) || empty($customerId) || empty($customerEmail)  
            || empty($customerName) || empty($orderId) || empty($productArray))
        {
            Mage::getSingleton('core/session')->addError('Invalid data while saving Rma Link details');
        }
        else 
        {
            try
            {
                foreach($rmaItemIdArray as $key => $rmaItemId)
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
                $url = Mage::getBaseUrl();
                $link = $url."rma/index/bankform/rmaItemId/".implode("-",$rmaItemIdArray);
                $subject = 'Return Received of OrderId '.$orderId;
                $message = 'Rma request in Return Received State';
                $resultMail = Mage::helper('rma')->sendMail($customerEmail,$customerName,$subject,$orderId,$productArray,$message,$link);
            }
            catch(Exception $e)
            {
                Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
                return;
            }
        }
    }

    public function checkForQuantity($rmaItemsArray) 
    {   
        if(empty($rmaItemsArray))
        {
            return;
        }
        try
        {
            $result = 0;
            foreach($rmaItemsArray as $key => $value)
            {
                $modelItem = Mage::getModel('rma/rma_item')->load($key);
                if($value['qty_approved'] > $modelItem->getQtyRequested())
                {
                    $result = 1;
                    break;
                }            
            }
            return $result;
        }
        catch(Exception $e)
        {
            echo $e->getMessage();
            return;
        }
    }
}
