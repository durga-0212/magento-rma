<?php

class Thycart_Rma_Block_Adminhtml_Rma_Edit_Tab_General_Shippingaddress
    extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Get order shipping address
     *
     * @return string|null
     */
    public function getOrderShippingAddress()
    {
        $data = Mage::registry('rma_data');
        $OrderModel = Mage::getModel('sales/order')->load($data->getOrderId());
        $address = $OrderModel->getShippingAddress();        
        if ($address instanceof Mage_Sales_Model_Order_Address) {
            return $address->format('html');
        } else {
            return null;
        }
    }
}
