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
    
    
     /*
      Customer order history
     */
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
                $orderInvoices = Mage::helper('rma')->orderInvoices($orderId);
                if(empty($orderInvoices))
                {
                    $cancelType = 1;
                }
                $productInfo = Mage::getModel('rma/order')->getProductsById($orderId);
                foreach($productInfo as $key => $value)
                {
                    $productModel = Mage::getModel('catalog/product')->load($value['product_id']);
                    $return = $productModel->getIsReturnable();
                    $productInfo[$key]['is_returnable'] =  $return;            
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
        $data = $this->getRequest()->getParams();
        $status = 'pending';
        if(isset($data['cancelType']) && $data['cancelType'] ==1)
        {
            $status = 'canceled';
        }
        $orderModel = Mage::getModel('rma/order'); 
        $date = Mage::getModel('core/date')->date('Y-m-d H:i:s');
        $customerModel = Mage::getSingleton('customer/session')->getCustomer();
        $orderModel->setData(array('order_id'=>$data['order_id'],'increment_id'=>$data['increment_id'],'order_increment_id'=>$data['increment_id'],'order_date'=>$data['order_date'],'date_requested'=>$date,'store_id'=> $data['store_id'],'customer_id'=>$customerModel->getEntityId(),'customer_name'=>$customerModel->getName(),'customer_email'=>$customerModel->getEmail(),'status'=>$status));
        if($orderModel->save())
        {
            foreach ($data['Product'] as $key => $value) 
            {
                if($value['checked'] ||  $data['cancelType'])
                {
                    $item_data=array(
                        'rma_entity_id' => $orderModel->getId(),
                        'qty_ordered'  => $value['qty_ordered'],
                        'product_name' => $value['name'],
                        'product_sku' => $value['sku'],
                        'order_item_id' => $value['item_id'],
                        'qty_requested' => $value['qty_requested'],
                        'product_options' => $value['product_options'],
                        'item_status' => $status
                    );
                  $rmaItemModel = Mage::getModel('rma/rma_item');  
                  $rmaItemModel->setData($item_data);
                  $rmaItemModel->save();
                  $this->_redirect('*/*/index');
                }           
            }  
            if(!isset($data['cancelType']) || $data['cancelType'] ==0)
            {
                $rmaHistoryModel = Mage::getModel('rma/rma_history');
                $rmaHistoryModel->setData(array('rma_entity_id'=> $orderModel->getId(),'is_visible_on_front'=>1,'comment'=>'Your RMA request has been placed','status'=>'pending','created_at'=>$date,'is_admin'=>1));
                $rmaHistoryModel->save();
            }
            $rmaAttributeModel = Mage::getModel('rma/rma_attributes');
            $rmaAttributeModel->setData(array('rma_entity_id'=> $orderModel->getId(),'resolution'=>$data['resolution_type'],'delivery_status'=>$data['delivery_status'],'reason'=>$data['reason'],'created_at'=>$date));
            $rmaAttributeModel->save();      
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
        if(!Mage::getSingleton('customer/session')->isLoggedIn())
        {
            $this->_redirect('customer/account/login');
        }
        else
        {
            $this->loadLayout();
            $this->renderLayout();        
            //Zend_Debug::dump($this->getLayout()->getUpdate()->getHandles());
        }
        
    }
    
    public function savebankdetailsAction()
    {
        $postData = $this->getRequest()->getParams();
        if($postData)
        {
            if(!empty('bankname') && !empty('account_no') && !empty('ifsc_code'))
            {
                $id = Mage::getSingleton('customer/session')->getCustomer()->getEntityId();
                $modelCustomer = Mage::getModel('customer/customer')->load($id);
                $modelCustomer->addData($postData);                
                $modelCustomer->save();
               
            }
            else 
            {
                Mage::getSingleton('core/session')->addError('Please fill all the details');
            }
        }
        else 
        {
            Mage::getSingleton('core/session')->addError('Data not posted');
        }
    }
<<<<<<< HEAD

    public function cancelOrderAction()
    {
        $this->loadLayout();
        $this->getLayout()->getBlock('head')->setTitle($this->__('Request Cancel Order'));
        $this->renderLayout();
       
=======
    
    public function notifyCustomerByEmail()
    {
        //$this->_redirect();
>>>>>>> anjali
    }
}
