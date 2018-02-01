<?php

class Thycart_Rma_Block_Adminhtml_Rma_Edit_Tab_General_Returnaddress
    extends Mage_Adminhtml_Block_Widget_Form
{

 
    public function getReturnAddress()
    {       
        $country = Mage::getStoreConfig('shipping/origin/country_id');
        $region_id = Mage::getStoreConfig('shipping/origin/region_id');
        $state=Mage::getModel('directory/region')->load($region_id,'region_id')->getName();
        $postcode = Mage::getStoreConfig('shipping/origin/postcode');
        return $country." ".$state."<br>".$postcode;
    }

}
