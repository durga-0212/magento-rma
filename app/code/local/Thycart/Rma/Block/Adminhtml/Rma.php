<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class Thycart_Rma_Block_Adminhtml_Rma extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {     
        $this->_controller = 'adminhtml_rma';
        $this->_blockGroup = 'rma';
        $this->_headerText = Mage::helper('rma')->__('RMA Grid');
        parent::__construct();
        $this->_removeButton('add');
    }
}

