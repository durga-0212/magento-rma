<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class Thycart_Rma_Model_Rma_History extends Mage_Core_Model_Abstract
{
    protected function _construct() 
    {
        $this->_init('rma/rma_history');
    }
    
    public function getHistoryCollection($id)
    {
        $history=Mage::getResourceModel('rma/rma_history_collection')
                 ->join(array('ro' => 'rma/order'), 'main_table.rma_entity_id = ro.entity_id', array(
                    'customer_name'))
                ->addFieldToFilter('rma_entity_id',$id);
        return $history;
    }
}
