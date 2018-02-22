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
        // Zend_Debug::dump($this->getLayout()->getUpdate()->getHandles());
    }
    
    public function saveCommentAction() 
    {
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
        if($this->getRequest()->isXmlHttpRequest())
        {
            $orderId = $this->getRequest()->getParam('OrderId');
            if($orderId > 0)
            {
                $cancelType = 0;
                $shipmentIds = Mage::helper('rma')->orderShipment($orderId);
                if(empty($shipmentIds))
                {
                    $cancelType = 1;
                }
                $productInfo = Mage::getModel('rma/order')->getProductsById($orderId);  
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
                    }
                }
                $productInfo['is_cancel'] =  $cancelType;            
                Mage::register('productInfo', $productInfo);
                $output = $this->getLayout()->createBlock('rma/return_order_request')->setTemplate('rma/return/ajaxproduct.phtml')->toHtml();
                $this->getResponse()->setBody($output);
            }
            else 
            {
                Mage::getSingleton('core/session')->addError('Something went wrong');
            }
        }
        else 
        {
            Mage::getSingleton('core/session')->addError('Something went wrong');
        }

    }
    
    public function saveAction()
    {   
        $date = Mage::getModel('core/date')->gmtDate('Y-m-d H:i:s');
        $data = $this->getRequest()->getParams();  
        $orderId = $data['order'];      
        $status = Thycart_Rma_Model_Rma_Status::STATE_PENDING;

        if(isset($data['cancelType']) && $data['cancelType'] ==1)
        {
            $status = Thycart_Rma_Model_Rma_Status::STATE_CANCELED;            
        }
        $customerModel = Mage::getSingleton('customer/session')->getCustomer();

        $orderModel = $this->saveRmaOrderData($customerModel, $orderId, $status);

        if($rmaOrderId)
        {
            foreach ($data['products'] as $key => $value) 
            {                
                if($value['checked'] ||  $data['cancelType'])
                {
                    //Pending Anjalee
                    $productInfo = Mage::getModel('rma/order')->getProductsById($data['order']);
                    $item_data=array(
                        'rma_entity_id' => $orderModel->getId(),
                        'qty_ordered'  => '3',
                        'product_name' => 'RMA Product',
                        'product_sku' => 'p003',
                        'order_item_id' => '50',
                        'product_id' => $value['product_id'],
                        'qty_requested' => $value['qty_requested'],
                        'item_status' => $status
                    );
                    $rmaItemModel = Mage::getModel('rma/rma_item');  
                    $rmaItemModel->setData($item_data);
                    $rmaItemModel->save();
                    $productName[] = $item_data['product_name'];
                
                }
                if($data['cancelType'])
                {
                    Mage::getModel('sales/order')->load($orderId)->cancel()->save();
                }
            }die;
            if(!isset($data['cancelType']) || $data['cancelType'] ==0)
            {
                $rmaHistoryModel = Mage::getModel('rma/rma_history');
                $rmaHistoryModel->setData(array('rma_entity_id'=> $orderModel->getId(),'is_visible_on_front'=>1,'comment'=>'Your RMA request has been placed','status'=>Thycart_Rma_Model_Rma_Status::STATE_PENDING,'created_at'=>$date,'is_admin'=>1));
                $rmaHistoryModel->save();
            }
            else
            {
               Mage::getModel('sales/order')->load($data['order_id'])->cancel()->save();
               
            }
            
            $rmaAttributeModel = Mage::getModel('rma/rma_attributes');
            $rmaAttributeModel->setData(array('rma_entity_id'=> $orderModel->getId(),'resolution'=>$data['resolution_type'],'delivery_status'=>$data['delivery_status'],'reason'=>$data['reason'],'created_at'=>$date));
            if($rmaAttributeModel->save())
            {
                $mailResult = $this->checkForSendingMail($data['cancelType'],$data['order_id'],$productName);            
            }
            $this->_redirect('*/*/index');
        }
    }
    
    public function calculatePriceAction()
    {
        if($this->getRequest()->isXmlHttpRequest())
        {
            $product_Qty = $this->getRequest()->getParam('product_Qty');
            $product_price = $this->getRequest()->getParam('product_price');
            $result = $product_Qty*$product_price;
            $this->getResponse()->setBody($result);   
        }
        else 
        {
            Mage::getSingleton('core/session')->addError('Something went wrong');
        }
    }
    
    public function bankFormAction() 
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
    
    public function savebankdetailsAction()
    {
        if(empty($this->getRequest()->getParam('bankname')) || empty($this->getRequest()->getParam('account_no')) || empty($this->getRequest()->getParam('ifsc_code')) || empty($this->getRequest()->getParam('rmaItemId')))
        {
            Mage::getSingleton('core/session')->addError('Please fill all the details');
            $this->_redirect('*/*/bankForm');
            return;
        }
        $rmaItemId = $this->getRequest()->getParam('rmaItemId');
        $postData = $this->getRequest()->getParams();
        $customerId = Mage::getSingleton('customer/session')->getCustomer()->getEntityId();
        $modelCustomer = Mage::getModel('customer/customer')->load($customerId);
        $modelCustomer->addData($postData);
        $updateCustomerDetails = $modelCustomer->save();
        if($updateCustomerDetails)
        {
            $this->changeRmaLinkStatus($rmaItemId,$customerId);
            $this->changeRmaItemStatus($rmaItemId);
            $this->_redirect('*/*/index');
            Mage::getSingleton('core/session')->addSuccess('Bank Details Saved Successfully');
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
    
    public function changeRmaLinkStatus($rmaItemId,$customerId)
    { 
        $rmaItemIdArray = explode("-",$rmaItemId);
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
    }
    
    public function changeRmaItemStatus($rmaItemId)
    { 
        $rmaItemIdArray = explode("-",$rmaItemId);
        foreach($rmaItemIdArray as $id)
        {
            $modelRmaItem = Mage::getModel('rma/rma_item')->load($id);
            $modelRmaItem->addData(array('item_status'=> Thycart_Rma_Model_Rma_Status::STATE_PAYMENT_REQUEST));
            $changeItemStatus = $modelRmaItem->save();
        }
        if($changeItemStatus)
        {
            $from = 'anjalee.singh@adapty.com';
            $to = 'anjalee.singh@adapty.com';
            $subject = 'Rma Payment Request';
            $body = 'Customer Bank Details have been saved';
            $resultMail = $this->sendMail($from,$to,$subject,$body);
        }
    }
    
    public function sendMail($from,$to,$subject,$body,$link='')
    {
        $resultMail = Mage::helper('rma')->sendMail($from,$to,$subject,$body,$link);
        return $resultMail;
    }
    
    public function getCustomerEmailId()
    {
        $emailId = Mage::getSingleton('customer/session')->getCustomer()->getEmail();
    }
    
    public function checkForSendingMail($cancelType='',$orderId,$productArray)
    {
        $productName = implode(",",$productArray);
        $from = 'anjalee.singh@adapty.com';
        $to = 'anjalee.singh@adapty.com';
        $subject = 'Return Request for OrderId '.$orderId;
        $body = 'Rma Request have been placed for products'.'<br>'.$productName;
        if($cancelType)
        {
            $subject = 'Order Cancellation for OrderId '.$orderId;
            $body = 'Order has been canceled for'.'<br>'.$productName;            
            $url = Mage::getBaseUrl();
            $link = $url."rma/index/bankform/";            
        }        
        $resultMail = $this->sendMail($from,$to,$subject,$body);
        return $resultMail;
    }

    public function getOrderedQtyById()
    {
        if($this->getRequest()->isXmlHttpRequest())
        {
            $product_Qty = $this->getRequest()->getParam('product_Qty');
            $product_price = $this->getRequest()->getParam('product_price');
            $result = $product_Qty*$product_price;
            $this->getResponse()->setBody($result);   
        }
        else 
        {
            Mage::getSingleton('core/session')->addError('Something went wrong');
        }
    }

    public function saveRmaOrderData($customerModel, $orderId, $orderInfo, $status)
    {
        $date = Mage::getModel('core/date')->gmtDate('Y-m-d H:i:s');
        
        $orderInfo = Mage::getModel('sales/order')->load($orderId);
        
        $lastInertId = 0;
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
        return $orderModel;
    }
}