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
            $orderId = $this->getRequest()->getParam('OrderId');
            try
            {
                $shipmentIds = Mage::helper('rma')->orderShipment($orderId);
               
                if(empty($shipmentIds))
                {
                    $cancelType = 1;
                }
                foreach($shipmentIds as $shipment)
                {
                    $shipmentData   = Mage::getResourceModel('sales/order_shipment_item_collection')
                        ->addFieldToSelect('product_id')
                        ->addFieldToFilter('parent_id',$shipment)
                        ->getData();
                    $productIds = array_column($shipmentData,'product_id');
                    $shipmentPids = array_merge($shipmentPids,$productIds);
                }
                
                $orderArray = array();
                $order = Mage::getModel('sales/order')->load($orderId);
                $productInfo = $order->getAllVisibleItems();
                $orderArray['shipping_charge']=$order->getShippingAmount();               
                $orderArray['is_cancel'] = $cancelType;
                $shipped_qty = array();
                foreach($productInfo as $product)
                {                   
                    $productModel = Mage::getModel('catalog/product')->load($product->getProductId());
                    $isReturnable = $productModel->getIsReturnable();
                    $product->setData('is_returnable', $isReturnable);
                    if($cancelType == 0)
                    {
                        $shipped_qty = Mage::getModel('rma/order')->getShippedQty($product->getItemId());           
                        $product->setData('qty_ordered',$shipped_qty);
                    }
                               
                    if($isReturnable)
                    {
                        $rmaProductsStatus = Mage::getModel('rma/order')->getRmaProductsByOrderItemId($product->getItemId());
                        $product->setData('is_returnable', 0); 
                        if(empty($rmaProductsStatus))
                        {
                            $product->setData('is_returnable', 1);
                        }
                        if($cancelType == 0)
                        {
                            if(!in_array($product->getProductId(), $shipmentPids))
                            {
                                $product->setData('is_returnable', 0); 
                            }
                        }
                    }
                    $orderArray['productDetails'][] = $product->getData();
                }           
                Mage::register('orderArray', $orderArray);              
                $output = $this->getLayout()->createBlock('rma/return_order_request')->setTemplate('rma/return/ajaxproduct.phtml')->toHtml();              
                $this->getResponse()->setBody($output);
            }
            catch(Exception $e)
            {
                Mage::getSingleton('core/session')->addError('Error while loading Product Information');
                echo $e->getMessage();
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
                $consignmentNo = $this->getRequest()->getParam('consign_number');
                $status = Thycart_Rma_Model_Rma_Status::STATE_PENDING;

                if(isset($data['cancelType']) && $data['cancelType'] ==1)
                {
                    $status = Thycart_Rma_Model_Rma_Status::STATE_CANCELED;            
                }
                $customerModel = Mage::getSingleton('customer/session')->getCustomer();

                $rmaOrderId = $this->saveRmaOrderData($customerModel, $orderId, $status, $consignmentNo);  
                
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
                        $rmaHistoryModel->setData(array(
                            'rma_entity_id'=> $rmaOrderId,
                            'is_visible_on_front'=>1,
                            'comment'=>'Your RMA request has been placed',
                            'status'=>$status,
                            'created_at'=>$date,
                            'is_admin'=>1)
                        );
                        $rmaHistoryModel->save();
                    }
                    else
                    {               
                       Mage::getModel('sales/order')->load($orderId)->cancel()->save();               
                    }

                    $rmaAttributeModel = Mage::getModel('rma/rma_attributes');
                    $rmaAttributeModel->setData(array(
                        'rma_entity_id'=> $rmaOrderId,
                        'resolution'=>$data['resolution_type'],
                        'delivery_status'=>$data['delivery_status'],
                        'reason'=>$data['reason'],
                        'created_at'=>$date)
                    );
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
    
    public function savebankdetailsAction()
    {
        if(empty($this->getRequest()->getParam('bankname')) || empty($this->getRequest()->getParam('account_no')) || 
            empty($this->getRequest()->getParam('ifsc_code')))
        {
            Mage::getSingleton('core/session')->addError('Please fill all the details');
            $this->_redirect('*/*/bankForm');
            return;
        }
        try
        {
            $rmaItemId = $this->getRequest()->getParam('rmaItemId');
            $rmaItemId = Mage::helper('rma')->decryptBankDetail($rmaItemId);
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
                if(isset($rmaItemId) && !empty($rmaItemId))
                {
                    $resultStatus = $this->changeRmaItemStatus($rmaItemIdArray,$customerModel);
                }
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
        $customerIdEncrypted = $this->getRequest()->getParam('customerId');
        $rmaItemIdEncrypted = $this->getRequest()->getParam('rmaItemId');
        $customerId = Mage::helper('rma')->decryptBankDetail($customerIdEncrypted);
        $rmaItemId = Mage::helper('rma')->decryptBankDetail($rmaItemIdEncrypted);
        $rmaItemIdArray = explode("-",$rmaItemId);
        $cust_sess_id=Mage::getSingleton('customer/session')->getCustomer()->getEntityId();
        $cust_bank_name =Mage::getSingleton('customer/session')->getCustomer()->getBankname();
        if(isset($customerId) && !empty($customerId))
        {   
            if(isset($cust_bank_name) && empty($cust_bank_name))
            {
                if($customerId != $cust_sess_id)
                {
                    session_destroy();
                    $sessionName = Mage::getSingleton('customer/session')->getSessionName();
                    Mage::getSingleton('core/cookie')->delete($sessionName);
                    $this->_redirect('customer/account/login');
                    return;
                }

            }
            else 
            {
                foreach($rmaItemIdArray as $rmaItemArray)
                {   
                    $rmaItemModel = Mage::getModel('rma/rma_item')->load($rmaItemArray);
                    if($rmaItemModel->getItemStatus() == Thycart_Rma_Model_Rma_Status::STATE_PAYMENT_REQUEST)
                    { 
                        Mage::getSingleton('core/session')->addSuccess('You have already filled Bank Details');
                        $this->_redirect('customer/account');
                        return;
                    }
                }
            }
            
        }
        $this->loadLayout();
        $this->renderLayout();

    }
    
    public function changeRmaItemStatus($rmaItemIdArray,$customerModel)
    {   
        if(empty($rmaItemIdArray) || empty($customerModel))
        {
            return;
        }
        try
        {
            $productArray = array();
            foreach($rmaItemIdArray as $id)
            {
                $modelRmaItem = Mage::getModel('rma/rma_item')->load($id);
                $modelRmaItem->addData(array(
                    'item_status'=> Thycart_Rma_Model_Rma_Status::STATE_PAYMENT_REQUEST,
                    'link_status'=>1));
                $changeItemStatus = $modelRmaItem->save();
                $name = $modelRmaItem->getProductName();
                $quantity = $modelRmaItem->getQtyApproved();
                $rmaEntityId = $modelRmaItem->getRmaEntityId();
                $productArray[$name] = $quantity;
            }
            $rmaModel = Mage::getModel('rma/order')->load($rmaEntityId);           
            $orderId = $rmaModel->getOrderId();
            if($changeItemStatus)
            {
                $subject = 'Payment Requested for OrderId '.$orderId;
                $message = "<h3>Payment Request</h3><br><span>Order Id ".$orderId."</span>";               
                $resultMail =  Mage::helper('rma')->sendMail($customerModel->getEmail(),$customerModel->getName(),$subject,$productArray,$message);
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
            $link = '';
            $message = "<h3>Rma Request in Pending State</h3><br><span>Order Id ".$orderId."</span>";
            $subject = 'Return Request for OrderId '.$orderId;
            if($cancelType)
            {
                $subject = 'Order Cancellation for OrderId '.$orderId;
                $message = "<h3>Order Cancellation Request</h3><br><span>Order Id ".$orderId."</span>";
                $url = Mage::getBaseUrl();
                if(empty($customerModel->getBankname()) || empty($customerModel->getAccountNo()) || empty($customerModel->getIfscCode()))
                {
                    $link = "<a href=".$url."rma/index/bank/>Please fill your bank details</a>";
                }
                else 
                {                   
                    $link = "<h3>Please Login to your account and verify Bank Details</h3>";
                }
                
            }   
            $resultMail = Mage::helper('rma')->sendMail($customerModel->getEmail(),$customerModel->getName(),$subject,$productArray,$message,$link);
            return $resultMail;
        }
        catch(Exception $e)
        {
            Mage::getSingleton('core/session')->addError($e->getMessage());
            return;
        }
    }

    public function saveRmaOrderData($customerModel, $orderId, $status, $consignmentNo='')
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
                'consignment_number'=>$consignmentNo,
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
        $totalShippedQty = 0;
        $totalRequestedQty = 0;
        try
        {   
            $orderModel = Mage::getModel('sales/order')->load($orderId);
            $cnt = $orderModel->getTotalItemCount();
            $cntChecked = 0;
            foreach ($productsArray as $key => $value) 
            {   
                if(isset($value['checked']) && !empty($value['checked']) || $cancelType )
                {   
                    if(!$cancelType)
                    {
                        $cntChecked += $value['checked'];
                    }
                    if(empty($value['qty_requested']))
                    {
                        Mage::getSingleton('core/session')->addError('Please fill all details');
                        $this->_redirect('*/*/addrequest/');
                        return false;
                    }
                    $productInfo = Mage::getModel('rma/order')->getProductInfo($key,$orderId);
                    $shippedQty = Mage::getModel('rma/order')->getShippedQty($productInfo['item_id']);
                    $totalShippedQty += $shippedQty;  
                    $totalRequestedQty += $value['qty_requested'];
                    if((!$cancelType && ($value['qty_requested'] > $shippedQty)) 
                        || ($cancelType && $value['qty_requested'] != $productInfo['qty_ordered']) )
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
                        'product_price' => $productInfo['base_original_price']*$value['qty_requested'],
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
            
            if(($totalShippedQty == $totalRequestedQty && $cnt == $cntChecked) || $cancelType )
            {                                  
                $shippingCharge = Mage::getModel('rma/order')->getShippingCharge($orderId);
                $orderModel=Mage::getModel('rma/order')->load($rmaOrderId);
                $orderModel->setData('shipping_charge',$shippingCharge);                    
                $orderModel->save();
            }
            
            return $productArray;
        }
        catch(Exception $e)
        {
            Mage::getSingleton('core/session')->addError('Error while Saving Data In Rma Item Table');
            echo $e->getMessage();
            $this->_redirect('*/*/addrequest');
            return;
        }
    }
}