<?php 
class Thycart_Rma_Block_Adminhtml_Rma_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId("rma_tabs");
        $this->setDestElementId("edit_form");
        $this->setTitle(Mage::helper("rma")->__("RMA"));
    }
    
    protected function _beforeToHtml()
    {
        $this->addTab("form_section", array(
        "label" => Mage::helper("rma")->__("RMA"),
        "title" => Mage::helper("rma")->__("RMA Detail"),
        "content" => $this->getLayout()->createBlock("rma/adminhtml_rma_edit_tab_form")->toHtml(),
        ));
        return parent::_beforeToHtml();
    }

}
?>