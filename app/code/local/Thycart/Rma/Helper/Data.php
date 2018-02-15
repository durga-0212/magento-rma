<?php 
class Thycart_Rma_Helper_Data extends Mage_Core_Helper_Abstract
{
     
    public function getAttributeOptionValues($attribute_code) {
        $attribute_data=Mage::getModel('rma/rma_eav_attribute')->getAttributeCollection();
        return $attribute_data[$attribute_code];
    }
    
    public function orderInvoices($orderId=0)
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
?>
