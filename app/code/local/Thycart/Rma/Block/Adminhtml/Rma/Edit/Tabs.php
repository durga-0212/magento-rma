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
    
//    protected function _beforeToHtml()
//    {
//        $this->addTab("form_section", array(
//        "label" => Mage::helper("rma")->__("General Information"),
//        "title" => Mage::helper("rma")->__("RMA Detail"),
//        "url"  =>  $this->getUrl('*/*/view', array('_current' => true)),      
//        ));
//        $this->addTab('Form_Rma_Grid', array(
//                'label'     => Mage::helper('rma')->__('Rma Grid'),
//                'url'       => $this->getUrl('*/*/productGrid', array('_current' => true)),
//                'class'     => 'ajax',
//            ));      
//        return parent::_beforeToHtml();
//    }

}
?>