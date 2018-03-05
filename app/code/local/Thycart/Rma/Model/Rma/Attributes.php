<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class Thycart_Rma_Model_Rma_Attributes extends Mage_Core_Model_Abstract
{
    protected function _construct() 
    {
        $this->_init('rma/rma_attributes');
    }
    
    public function getAttributesCollection($id)
    {
        if(empty($id))
        {
            return;
        }
        try
        {
        $products=Mage::getResourceModel('rma/rma_attributes_collection')
            ->join(array('rfoi' => 'rma/rma_item'), 'main_table.rma_entity_id = rfoi.rma_entity_id', array(
                '*'))                
            ->addFieldToFilter('main_table.rma_entity_id',$id);              
        return $products;
        }
        catch(Exception $e)
        {
            echo $e->getMessage();
            return;
        }
    }
}

