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
    
    protected function _beforeToHtml()
    {
        $this->addTab("form_section", array(
        "label" => Mage::helper("rma")->__("General Information"),
        "title" => Mage::helper("rma")->__("General Information"),
        "content" => $this->getLayout()->createBlock("rma/adminhtml_rma_item_attribute_edit_tab_form")->toHtml(),      
        ));
        $this->addTab("attribute_options", array(
        "label" => Mage::helper('rma')->__('Attribute Options'),
        "title" => Mage::helper("rma")->__("Attribute Options"),
        'url'     => $this->getUrl('*/*/view', array('_current' => true)),
        'class' => 'ajax',
        ));      
        return parent::_beforeToHtml();
    }
}
