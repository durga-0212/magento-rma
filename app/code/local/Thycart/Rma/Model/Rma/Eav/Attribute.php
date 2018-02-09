<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class Thycart_Rma_Model_Rma_Eav_Attribute extends Mage_Core_Model_Abstract
{
    protected function _construct() 
    {
        $this->_init('rma/rma_eav_attribute');
    }
    
    public function getAttributeCollection() {
       $collectionData= Mage::getModel('rma/rma_eav_attribute')->getCollection()
            ->addFieldToSelect('*')
            ->join(array('reao'=>'rma/rma_eav_attributeoption'),'main_table.attribute_id=reao.attribute_id')
            ->addFieldToSelect('*')->getData();
       foreach ($collectionData as $key => $value) {           
          $att_values[$value['attribute_code']][$key] =$value['value'];
        }        
    return $att_values;      
    }
}
