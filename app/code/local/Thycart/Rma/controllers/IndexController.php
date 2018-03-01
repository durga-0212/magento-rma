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

        if (!Mage::getSingleton('customer/session')->authenticate($this, $loginUrl)) 
        {
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
        }
    }
    
    public function indexAction()
    {      
        $this->loadLayout();
        $this->getLayout()->getBlock('head')->setTitle($this->__('My Rma Returns History'));
        $this->renderLayout();
    }
    
    public function addrequestAction() 
    {
        $this->loadLayout();
        $this->getLayout()->getBlock('head')->setTitle($this->__('Create RMA'));
        $this->renderLayout();
    }
        
    public function viewAction()
    {
        $this->loadLayout();        
        $this->getLayout()->getBlock('head')->setTitle($this->__('My Rma Returns History'));
        $this->renderLayout();
    }
    
    public function saveCommentAction() 
    {
        if(empty($this->getRequest()->getParam('comment')) || empty($this->getRequest()->getParam('rma_entity_id')) || 
            empty($this->getRequest()->getParam('status')))
        {
            Mage::getSingleton('core/session')->addError('Please Add Comment');
            return;
        }
        try
        {
            $postData = $this->getRequest()->getParams();
            $postData['created_at'] = Mage::getModel('core/date')->gmtDate('Y-m-d H:i:s');      
            $modelObj = Mage::getModel('rma/rma_history')->setData($postData)->save();
            if($modelObj)
            {
                $url = Mage::helper('core/http')->getHttpReferer() ? Mage::helper('core/http')->getHttpReferer():$this->_getRefererUrl();
                Mage::app()->getResponse()->setRedirect($url);  
            }
        }
        catch(Exception $e)
        {
            Mage::getSingleton('core/session')->addError('Error while submitting comment');
            $this->_redirect('*/*/view',array("id" => $this->getRequest()->getParam("rma_id")));
            return;
        }
    }
    
    public function productinfoAction()
    { 
        if($this->getRequest()->isXmlHttpRequest())
        {
            if(empty($this->getRequest()->getParam('OrderId')) || $this->getRequest()->getParam('OrderId') <= 0)
            {
                Mage::getSingleton('core/session')->addError('Error while loading Product Information');
                return;
            }
            $cancelType = 0;
            $productIds = '';
            $shipmentPids = array();
            $orderId      = $this->getRequest()->getParam('OrderId');
            try
            {
                $shipmentIds = Mage::helper('rma')->orderShipment($orderId);
                if(empty($shipmentIds))
                {
                    $cancelType = 1;
                }
                $productInfo = Mage::getModel('rma/order')->getProductsById($orderId);  
                $order = Mage::getModel('sales/order')->load($orderId);                
                foreach($shipmentIds as $shipment)
                {
                    $shipmentData   = Mage::getResourceModel('sales/order_shipment_item_collection')
                        ->addFieldToSelect('product_id')
                        ->addFieldToFilter('parent_id',$shipment)
                        ->getData();
                    $productIds = array_column($shipmentData,'product_id');
                    $shipmentPids = array_merge($shipmentPids,$productIds);
                }
                
                foreach($productInfo as $key => $value)
                {
                    $productModel = Mage::getModel('catalog/product')->load($value['product_id']);
                    $isReturnable = $productModel->getIsReturnable();
                    $productInfo[$key]['is_returnable'] =  $isReturnable;            
                    if($isReturnable)
                    {
                        $rmaProductsStatus = Mage::getModel('rma/order')->getRmaProductsByOrderItemId($value['item_id']);
                        $productInfo[$key]['is_returnable'] =  0;
                        if(empty($rmaProductsStatus))
                        {
                            $productInfo[$key]['is_returnable'] =  1;
                        }
                        if($cancelType == 0)
                        {
                            if(!in_array($value['product_id'], $shipmentPids))
                            {
                                $productInfo[$key]['is_returnable'] =  0;
                            }
                        }
                    }
                }
            
                $productInfo['is_cancel'] =  $cancelType;            
                Mage::register('productInfo', $productInfo);
                $output = $this->getLayout()->createBlock('rma/return_order_request')->setTemplate('rma/return/ajaxproduct.phtml')->toHtml();
                $this->getResponse()->setBody($output);
            }
            catch(Exception $e)
            {
                Mage::getSingleton('core/session')->addError('Error while loading Product Information');
                return;
            }
        }
        else 
        {
            Mage::getSingleton('core/session')->addError('Error while loading Product Information');
        }
        

    }
    
    public function saveAction()
    {   
        if(empty($this->getRequest()->getParam('order')) || empty($this->getRequest()->getParam('products')) 
            || empty($this->getRequest()->getParam('resolution_type')) || empty($this->getRequest()->getParam('delivery_status'))
            || empty($this->getRequest()->getParam('reason')))
        {
            Mage::getSingleton('core/session')->addError('Please fill all the details');
            $this->_redirect('*/*/addrequest/');
            return;
        }        
        else 
        {
            try
            {
                $modelResource = Mage::getSingleton('core/resource')->getConnection('core_write');              
                $modelResource->beginTransaction();
                $date = Mage::getModel('core/date')->gmtDate('Y-m-d H:i:s');
                $data = $this->getRequest()->getParams();        
                $orderId = $data['order'];      
                $status = Thycart_Rma_Model_Rma_Status::STATE_PENDING;

                if(isset($data['cancelType']) && $data['cancelType'] ==1)
                {
                    $status = Thycart_Rma_Model_Rma_Status::STATE_CANCELED;            
                }
                $customerModel = Mage::getSingleton('customer/session')->getCustomer();

                $rmaOrderId = $this->saveRmaOrderData($customerModel, $orderId, $status);  
                
                if($rmaOrderId)
                { 
                    $productArray = $this->saveRmaOrderItemData($data['products'],$data['cancelType'],$orderId,$rmaOrderId,$status);
                    
                    if(!$productArray)
                    {
                        $modelResource->rollBack();
                        return;
                    }
                    if(!isset($data['cancelType']) || $data['cancelType'] == 0)
                    {                
                        $rmaHistoryModel = Mage::getModel('rma/rma_history');
                        $rmaHistoryModel->setData(array('rma_entity_id'=> $rmaOrderId,'is_visible_on_front'=>1,'comment'=>'Your RMA request has been placed','status'=>$status,'created_at'=>$date,'is_admin'=>1));
                        $rmaHistoryModel->save();
                    }
                    else
                    {               
                       Mage::getModel('sales/order')->load($orderId)->cancel()->save();               
                    }

                    $rmaAttributeModel = Mage::getModel('rma/rma_attributes');
                    $rmaAttributeModel->setData(array('rma_entity_id'=> $rmaOrderId,'resolution'=>$data['resolution_type'],'delivery_status'=>$data['delivery_status'],'reason'=>$data['reason'],'created_at'=>$date));
                    if($rmaAttributeModel->save())
                    {
                        $mailResult = $this->checkForSendingMail($data['cancelType'],$orderId,$productArray,$customerModel);            
                    }
                    $this->_redirect('*/*/index');
                }
                $modelResource->commit();
            }
            catch(Exception $e)
            {
                $modelResource->rollBack();
                $this->_redirect('*/*/addrequest');
                Mage::getSingleton('core/session')->addError('RMA Request is not generated');
            }
        }
    }
    
    public function bankFormAction() 
    {
        if(empty($this->getRequest()->getParam('rmaItemId')))
        {
            return;
        }
        try
        {
            $id = $this->getRequest()->getParam('rmaItemId');
            $validRma = $this->verifyRmaLinkDetails($id);
            if($validRma)
            {
                $this->loadLayout();
                $this->renderLayout();
            }
            else
            {
                $this->_redirect('customer/account/');
                Mage::getSingleton('core/session')->addError('You have already filled bank details');
            }
        }
        catch(Exception $e)
        {
            Mage::getSingleton('core/session')->addError('Error while loading Bank Form');
        }
    }
    
    public function savebankdetailsAction()
    {
        if(empty($this->getRequest()->getParam('bankname')) || empty($this->getRequest()->getParam('account_no')) || 
            empty($this->getRequest()->getParam('ifsc_code')) || empty($this->getRequest()->getParam('rmaItemId')))
        {
            Mage::getSingleton('core/session')->addError('Please fill all the details');
            $this->_redirect('*/*/bankForm');
            return;
        }
        try
        {
            $rmaItemId = $this->getRequest()->getParam('rmaItemId');
            $rmaItemIdArray = explode("-",$rmaItemId);
            $postData = $this->getRequest()->getParams();
            $customerModel = Mage::getSingleton('customer/session')->getCustomer();
            $customerId = $customerModel->getEntityId();
            $modelCustomer = Mage::getModel('customer/customer')->load($customerId);
            $accountNo = Mage::helper('rma')->encryptBankDetail($postData['account_no']);
            $postData['account_no'] = $accountNo;
            $modelCustomer->addData($postData);
            $updateCustomerDetails = $modelCustomer->save();
            if($updateCustomerDetails)
            {
                $result = $this->changeRmaLinkStatus($rmaItemIdArray,$customerId);
                if(!$result)
                {
                    Mage::getSingleton('core/session')->addError('Error while saving Bank Details');
                    $this->_redirect('*/*/bankForm');
                    return;
                }
                $resultStatus = $this->changeRmaItemStatus($rmaItemIdArray,$customerModel);
                $this->_redirect('*/*/index');
                Mage::getSingleton('core/session')->addSuccess('Bank Details Saved Successfully');
            }
        }
        catch(Exception $e)
        {
            Mage::getSingleton('core/session')->addError('Error while saving Bank Details');
            $this->_redirect('*/*/bankForm');
            return;
        }
    }

    public function cancelOrderAction()
    {
        $this->loadLayout();
        $this->getLayout()->getBlock('head')->setTitle($this->__('Request Cancel Order'));
        $this->renderLayout();      
    }
    
    public function bankAction()
    {   
        $this->loadLayout();
        $this->renderLayout();
    }
    
    public function verifyRmaLinkDetails($id)
    {   
        if(empty($id))
        {
            return;
        }
        try
        {
            $idArray = explode("-",$id);
            $modelCollection = Mage::getResourceModel('rma/link_collection')
                ->addFieldToSelect('status')
                ->addFieldToFilter('rma_order_item_id',array('in' => $idArray));            

            $collectionData = $modelCollection->getData();
            if(empty($collectionData))
            {
                return 0;
            }
            $statusArray = array_column($collectionData, 'status');
            if(in_array(1,$statusArray))
            {
                return 0;
            }
            return 1;
        }
        catch(Exception $e)
        {
            echo $e->getMessage();
            return;
        }
    }
    
    public function changeRmaLinkStatus($rmaItemIdArray,$customerId)
    { 
        if(empty($rmaItemIdArray) || empty($customerId))
        {
            return; 
        }
        try
        {
            $entityIdArray = Mage::getResourceModel('rma/link_collection')
                ->addFieldToSelect('entity_id')
                ->addFieldToFilter('customer_id',$customerId)
                ->addFieldToFilter('rma_order_item_id',array('in'=>$rmaItemIdArray));

            foreach($entityIdArray as $key => $value)
            {
                $modelRmaLink = Mage::getModel('rma/link')->load($value['entity_id']);            
                $modelRmaLink->addData(array('status'=>1));
                $result = $modelRmaLink->save();
            }
            return $result;
        }
        catch(Exception $e)
        {
            echo $e->getMessage();
            return;
        }
    }
    
    public function changeRmaItemStatus($rmaItemIdArray,$customerModel)
    {   
        if(empty($rmaItemIdArray) || empty($customerModel))
        {
            return;
        }
        try
        {
            foreach($rmaItemIdArray as $id)
            {
                $modelRmaItem = Mage::getModel('rma/rma_item')->load($id);
                $modelRmaItem->addData(array('item_status'=> Thycart_Rma_Model_Rma_Status::STATE_PAYMENT_REQUEST));
                $changeItemStatus = $modelRmaItem->save();
            }
            if($changeItemStatus)
            {
                $subject = 'Payment Requested for Rma';
                $message = 'Payment Request';
                $emailDetails = Mage::registry('emailDetails');
                $resultMail =  Mage::helper('rma')->sendMail($customerModel->getEmail(),$customerModel->getName(),$subject,$emailDetails[1],$emailDetails[0],$message);
            }
            return $changeItemStatus;
        }
        catch(Exception $e)
        {
            echo $e->getMessage();
            return;
        }
    }
    
    public function checkForSendingMail($cancelType='',$orderId,$productArray,$customerModel)
    {
        if(empty($orderId) || empty($productArray) || empty($customerModel))
        {
            return;
        }
        try
        {
            $message = "Rma Request in Pending State";
            $subject = 'Return Request for OrderId '.$orderId;
            if($cancelType)
            {
                $subject = 'Order Cancellation for OrderId '.$orderId;
                $message = "Order Cancellation Request";
                $url = Mage::getBaseUrl();
                $link = $url."rma/index/bankform/";                
            }   
            
            $resultMail = Mage::helper('rma')->sendMail($customerModel->getEmail(),$customerModel->getName(),$subject,$orderId,$productArray,$message);
            return $resultMail;
        }
        catch(Exception $e)
        {
            Mage::getSingleton('core/session')->addError('Error in Sending Email while Rma Request is created');
            return;
        }
    }

    public function saveRmaOrderData($customerModel, $orderId, $status)
    {
        if(empty($customerModel) || empty($orderId) || empty($status))
        {
            return;
        }
        try
        {
            $date = Mage::getModel('core/date')->gmtDate('Y-m-d H:i:s');        
            $orderInfo = Mage::getModel('sales/order')->load($orderId);
            $orderModel = Mage::getModel('rma/order'); 
            $orderModel->setData(array(
                'order_id'=>$orderId,
                'increment_id'=>$orderInfo->getIncrementId(),
                'order_increment_id'=>$orderInfo->getIncrementId(),
                'order_date'=>$orderInfo->getCreatedAt(),
                'date_requested'=>$date,
                'store_id'=> $orderInfo->getStoreId(),
                'customer_id'=>$customerModel->getEntityId(),
                'customer_name'=>$customerModel->getName(),
                'customer_email'=>$customerModel->getEmail(),
                'status'=>$status)
            );
            $orderModel->save();
            return $orderModel->getId();
        }
        catch(Exception $e)
        {
            Mage::getSingleton('core/session')->addError('Error while Saving Data In Rma Table');
            return;
        }
    }
    
    public function saveRmaOrderItemData($productsArray,$cancelType,$orderId,$rmaOrderId,$status)
    {        
        if(empty($productsArray) || empty($orderId) || empty($rmaOrderId) || empty($status))
        {            
            return;
        }
        $productArray = array();
        try
        {
            foreach ($productsArray as $key => $value) 
            {  
                if($value['checked'] ||  $cancelType )
                {                
                    if(empty($value['qty_requested']))
                    {
                        Mage::getSingleton('core/session')->addError('Please fill all details');
                        $this->_redirect('*/*/addrequest/');
                        return false;
                    }
                    $productInfo = Mage::getModel('rma/order')->getProductInfo($key,$orderId);
                    if($value['qty_requested'] > $productInfo['qty_ordered'])
                    {
                        Mage::getSingleton('core/session')->addError('You can not return '.$value['qty_requested'].' '.$productInfo['name']);
                        $this->_redirect('*/*/addrequest/');
                        return false;
                    }
                    $item_data=array(
                        'rma_entity_id' => $rmaOrderId,
                        'qty_ordered'  => $productInfo['qty_ordered'],
                        'product_name' => $productInfo['name'],
                        'product_sku' => $productInfo['sku'],
                        'order_item_id' => $productInfo['item_id'],
                        'product_id' => $key,
                        'qty_requested' => $value['qty_requested'],
                        'item_status' => $status
                    );
                    $rmaItemModel = Mage::getModel('rma/rma_item');  
                    $rmaItemModel->setData($item_data);                
                    $rmaItemModel->save();
                    $prodName = $item_data['product_name'];
                    $prodQty = $item_data['qty_requested'];
                    $productArray[$prodName] = $prodQty;
                }
            }
            return $productArray;
        }
        catch(Exception $e)
        {
            Mage::getSingleton('core/session')->addError('Error while Saving Data In Rma Item Table');
            return;
        }
    }
}