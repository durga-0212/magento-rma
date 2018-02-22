<?php

require_once 'Mage/Sales/controllers/OrderController.php';
class Thycart_Rma_OrderController extends Mage_Sales_OrderController
{

    public function historyAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('catalog/session');

        $this->getLayout()->getBlock('head')->setTitle($this->__('My Orders'));
        $orderList = $this->getLayout()->getBlock('sales.order.history');
        
        $orders = $orderList->getOrders();        
        if(!empty($orders->getData()))
        {         
            foreach ($orders as $key => $order) 
            {
                $order->setshowCancelBtn(0);
                $shipmentIds = Mage::helper('rma')->orderShipment($order['entity_id']);
                $status = $order->getStatus();

                if(empty($shipmentIds) &&  strtolower($status)!= Mage_Sales_Model_Order::STATE_CANCELED)
                {
                    $order->setshowCancelBtn(1);
                }
            }
        }
        $orderList->setOrders($orders);

        if ($block = $this->getLayout()->getBlock('customer.account.link.back')) {
            $block->setRefererUrl($this->_getRefererUrl());
        }
        $this->renderLayout();
    }

}