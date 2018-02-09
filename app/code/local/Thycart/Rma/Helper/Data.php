<?php 
class Thycart_Rma_Helper_Data extends Mage_Core_Helper_Abstract
{
     
    public function getAttributeOptionValues($attribute_code) {
        $attribute_data=Mage::getModel('rma/rma_eav_attribute')->getAttributeCollection();
        return $attribute_data[$attribute_code];
    }
    

}
?>
