<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class Test_Rma_Model_Order extends Mage_Core_Model_Abstract
{
    protected function _construct() 
    {
        $this->init('rma/order');
    }
}
