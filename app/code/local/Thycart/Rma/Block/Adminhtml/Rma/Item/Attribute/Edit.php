<?php 
class Thycart_Rma_Block_Adminhtml_Rma_Item_Attribute_Edit
    extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_objectId    = 'id';
        $this->_blockGroup  = 'rma';
        $this->_controller  = 'adminhtml_rma_item_attribute';

        parent::__construct();

        //$this->_updateButton("save", "label", Mage::helper("rma")->__("Save Attribute"));
        $this->_updateButton("delete", "label", Mage::helper("rma")->__("Delete"));


        $this->_addButton("saveandcontinue", array(
                "label"     => Mage::helper("rma")->__("Save And Continue Edit"),
                "onclick"   => "saveAndContinueEdit()",
                "class"     => "save",
        ), -100);

        $this->_formScripts[] = "
            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }";
        
    }
    

    /**
     * Return header text for edit block
     *
     * @return string
     */
    public function getHeaderText()
    {
        if( Mage::registry('attribute_data') && Mage::registry('attribute_data')->getId() )
        {
            return Mage::helper("rma")->__("Edit RMA Attribute #%s",$this->htmlEscape(Mage::registry('attribute_data')->getId()));
        }
        else
        {
            return Mage::helper("rma")->__("Add RMA Attribute");
        }
    }

    /**
     * Return validation url for edit form
     *
     * @return string
     */
//    public function getValidationUrl()
//    {
//        return $this->getUrl('*/*/validate', array('_current' => true));
//    }

    /**
     * Return save url for edit form
     *
     * @return string
     */
//    public function getSaveUrl()
//    {
//        return $this->getUrl('*/*/save', array('_current' => true, 'back' => null));
//    }
}

