<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class Thycart_Rma_Model_Resource_Rma_Eav_Attributeoption extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct() 
    {
         $this->_init('rma/rma_eav_attributeoption', 'entity_id');   
    }
}
