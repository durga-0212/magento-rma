<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class Thycart_Rma_Block_Adminhtml_Rma_Item_Attribute_Edit_Tabs
    extends Mage_Adminhtml_Block_Widget_Tabs
{
    /**
     * Initialize edit tabs
     *
     */
    public function _construct()
    {
        parent::_construct();

        $this->setId('rma_item_attribute_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('rma')->__('Attribute Information'));
    }
}
