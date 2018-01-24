<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class Thycart_Rma_Block_Adminhtml_Rma_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
        $this->_objectId = "id";
        $this->_blockGroup = "rma";
        $this->_controller = "adminhtml_rma";
        $this->_updateButton("save", "label", Mage::helper("rma")->__("Save Message"));
        $this->_updateButton("delete", "label", Mage::helper("rma")->__("Delete"));



        $this->_addButton("saveandcontinue", array(
                "label"     => Mage::helper("rma")->__("Save And Continue Edit"),
                "onclick"   => "saveAndContinueEdit()",
                "class"     => "save",
        ), -100);

        $this->_formScripts[] = "
            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
                        ";
    }
    
     public function getHeaderText()
    {
        if( Mage::registry('rma_data') && Mage::registry('rma_data')->getId() )
         {
              return Mage::helper("rma")->__("Edit RMA Order %s",$this->htmlEscape(Mage::registry('rma_data')->getIncrementId()));
         }
         else
         {
             return Mage::helper("rma")->__("Add RMA");
         }
    }
}
