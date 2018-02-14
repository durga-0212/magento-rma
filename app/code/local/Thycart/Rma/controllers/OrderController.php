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

        $orders = $orderList->getOrders()->getData();
        if(!empty($orders))
        {
            foreach ($orders as $key => $order) 
            {
                $orders[$key]['showCancelBtn'] = 0;
                $orderObject = Mage::getModel('sales/order')->load($order['entity_id']);
                $invoiceIds = $orderObject->getInvoiceCollection()->getAllIds();
                if(!empty($invoiceIds))
                {
                    $orders[$key]['show'] = 1;
                }
            }
        }
        $orderList->setOrders($orders);
        $_products = Mage::getResourceModel('catalog/product_collection')
           ->addAttributeToSelect(array('name', 'product_url', 'small_image'))
           ->addAttributeToFilter('sku', array('like' => 'UX%'))
            ->load();

        if ($block = $this->getLayout()->getBlock('customer.account.link.back')) {
            $block->setRefererUrl($this->_getRefererUrl());
        }
        $this->renderLayout();
    }
}