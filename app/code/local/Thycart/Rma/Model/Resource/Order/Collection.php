<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class Thycart_Rma_Model_Resource_Order_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    
      /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix    = 'rma_order_collection';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject    = 'order_collection';

    /**
     * Model initialization
     *
     */
    protected function _construct() 
    {
        $this->_init('rma/order');
        $this
            ->addFilterToMap('entity_id', 'main_table.entity_id')
            ->addFilterToMap('customer_id', 'main_table.customer_id');
    }
}