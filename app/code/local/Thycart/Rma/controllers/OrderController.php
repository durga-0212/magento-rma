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
                $invoiceIds = $this->OrderType($order['entity_id']);

                if(empty($invoiceIds))
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

    public function OrderType($orderId=0)
    {
        $invoiceIds = array();
        if(empty($orderId))
        {
            return $invoiceIds;
        }

        $orderObject = Mage::getModel('sales/order')->load($orderId);
        $invoiceIds = $orderObject->getInvoiceCollection()->getAllIds();
        return $invoiceIds;
    }
}