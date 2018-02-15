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
    
     public function getTrackingNumber()
    {
        $digits_needed = 8;
        $random_number = ''; // set up a blank string
        $count = 0;  
        $carriers=$this->getEnabledshippingmethods(); 
        
         $random_number .= $carriers;        
          while ($count < $digits_needed) {
            $random_digit = mt_rand(0, 9);                     
            $random_number .= $random_digit;
            $count++;
           }          
        return $random_number;
    }
    
    public function getEnabledshippingmethods()
    {
        $methods = Mage::getSingleton('shipping/config')->getAllCarriers();    
        foreach ($methods as $code => $carrier) {            
                $carriers[$code] = $carrier->getConfigData('title');           
        }        
        $arr=array();
        foreach($carriers as $key=> $value)
        {
            $arr[$key]=$value.'_'.$key.'_';           
        }
         $k = array_rand($arr);        
         $v = $arr[$k];        
        return $v;
    }
    
    public function getTrackingResponse($shipData=array()) {    
        $ordershipdata=Mage::getModel('rma/order')->getshipmentData($shipData);
        return $ordershipdata;    
    }
    
    
    
}
?>
