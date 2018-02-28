<?php 
class Thycart_Rma_Block_Adminhtml_Rma_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId("rma_tabs");
        $this->setDestElementId("edit_form");
        $this->setTitle(Mage::helper("rma")->__("RMA Information"));
    }
}
?>