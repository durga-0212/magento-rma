<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Thycart_Rma_Model_Order extends Mage_Core_Model_Abstract {

    protected function _construct() 
    {
        $this->_init('rma/order');
    }

    public function getOrdersById($dateRange = 0) 
    {   
        try
        {
            $customerId = Mage::getSingleton('customer/session')->getCustomer()->getId();
            $orderInfo = Mage::getResourceModel("sales/order_collection")
                ->addFieldToFilter('customer_id', Mage::getSingleton('customer/session')->getCustomer()->getId())
                ->addFieldToFilter('status', array('neq' => Mage_Sales_Model_Order::STATE_CANCELED));                

            if ($dateRange) 
            {
                $configDays = Mage::getStoreConfig('rma_section/rma_group/rma_days');
                $currentDate = date('Y-m-d H:i:s');
                $date = date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s", strtotime($currentDate)) . -$configDays . "  day"));
                $orderInfo->addAttributeToFilter('created_at', array('from' => $date, 'to' => $currentDate));
            }
            return $orderInfo;
        }
        catch(Exception $e)
        {
            echo $e->getMessage();
            return;
        }
    }

    public function getRmaProductsByOrderItemId($orderItemId) 
    {
        $rmaProductStatus = array();
        if (empty($orderItemId)) 
        {
            return $rmaProductStatus;
        }
        try
        {
            $rmaProductStatus = Mage::getModel('rma/order')->getCollection()
                ->addFieldToSelect('entity_id')
                ->join(array('roi' => 'rma/rma_item'), 'main_table.entity_id = roi.rma_entity_id', array('roi.item_status', 'roi.product_name'))
                ->addFieldToFilter('roi.order_item_id', $orderItemId)
                ->getData();
            return $rmaProductStatus;
        }
        catch(Exception $e)
        {
            echo $e->getMessage();
            return;
        }
    }

    public function getAllRmas() 
    {   
        try
        {
            $returns = Mage::getResourceModel('rma/order_collection')
                ->join(array('sfo' => 'sales/order'), 'main_table.order_id = sfo.entity_id', array(
                    'grand_total'))
                ->addFieldToFilter('customer_id', Mage::getSingleton('customer/session')->getCustomer()->getId())
                ->setOrder('date_requested', 'desc');
            return $returns;
        }
        catch(Exception $e)
        {
            echo $e->getMessage();
            return;
        }
    }

    public function getshipmentData($shipData = array()) 
    {   
        try
        {
            $ordershipcollection = Mage::getModel('sales/order')->getCollection()
                ->addAttributeToSelect('*')
                ->join(array('sfo' => 'sales/order_item'), 'main_table.entity_id = sfo.order_id', array(
                    'sfo.*'))
                ->addAttributeToSelect('*')
                ->join(array('sfg' => 'sales/order_grid'), 'sfo.item_id = sfg.entity_id', array(
                    'sfg.shipping_name'))
                ->addAttributeToFilter('sfo.order_id', $shipData['order_id'])
                ->addAttributeToFilter('sfo.item_id', $shipData['item_id']);
            return $ordershipcollection;
        }
        catch(Exception $e)
        {
            echo $e->getMessage();
            return;
        }
    }

    public function getShipmentDetails($shipData = array()) 
    {   
        try
        {
            $shipmentCollection = Mage::getResourceModel('sales/order_shipment_collection')
                ->addAttributeToSelect('entity_id')
                ->addAttributeToSelect('order_id')
                ->join(array('sfo' => 'sales/shipment_item'), 'main_table.entity_id = sfo.parent_id', array(
                    'sfo.order_item_id'))
                ->addAttributeToFilter('main_table.order_id', $shipData['order_id'])
                ->addAttributeToFilter('sfo.order_item_id', $shipData['item_id']);
            return $shipmentCollection->getData();
        }
        catch(Exception $e)
        {
            echo $e->getMessage();
            return;
        }
    }

    public function getRmaOrders() 
    {
        $rmaOrders = array();
        try
        {
            $rmaOrdersCollection = Mage::getModel('rma/order')->getCollection()
                ->join(array('roi' => 'rma/rma_item'), 'main_table.entity_id = roi.rma_entity_id', array('count(roi.rma_entity_id) as itemcount'))
                ->addFieldToSelect('order_id')
                ->addFieldToFilter('customer_id', Mage::getSingleton('customer/session')->getCustomer()->getId());
            $rmaOrdersCollection->getSelect()
                ->group('order_id');
            $rmaOrdersData = $rmaOrdersCollection->getData();
            if (empty($rmaOrdersData)) 
            {
                return $rmaOrders;
            }
            $finalData = array();
            foreach ($rmaOrdersData as $key => $order) 
            {
                $finalData[$key]['order_id'] = $order['order_id'];
                $finalData[$key]['itemcount'] = $order['itemcount'];
            }
            return $finalData;
        }
        catch(Exception $e)
        {
            echo $e->getMessage();
            return;
        }
    }

    public function getProductInfo($productId, $orderId) 
    {
        if(empty($productId) || empty($orderId))
        {
            Mage::getSingleton('core/session')->addError('Please fill details');
            $this->_redirect('*/*/addrequest/');
            return false;
        }
        try 
        {
            $productInfo = Mage::getResourceModel('sales/order_item_collection')
                ->addFieldToSelect(array('name', 'sku', 'qty_ordered', 'item_id','base_original_price'))
                ->addFieldToFilter('product_id', $productId)
                ->addFieldToFilter('order_id', $orderId)
                ->getData();
            
        } 
        catch (Exception $e) 
        {
            Mage::getSingleton("core/session")->addError('RMA Request is not generated');
            $this->_redirect("*/*/addrequest/");
            return;
        }
        return reset($productInfo);
    }
    
    public function getShippedQty($itemId)
    {
        if(empty($itemId))
        {
            return;
        }    
        try
        {
            $shipped_qty = Mage::getResourceModel('sales/order_shipment_item_collection')
                            ->addFieldToSelect('qty')
                            ->addFieldToFilter('order_item_id',$itemId)
                            ->getFirstItem()
                            ->getData();

            return reset($shipped_qty);
        }
        catch(Exception $e)
        {
            echo $e->getMessage();
            return;
        }
    }

}
