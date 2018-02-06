<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class Thycart_Rma_Block_Adminhtml_Rma_Item_Attribute_Edit_Tab_Js extends Mage_Adminhtml_Block_Widget_Form
{
    public function _construct()
    {
        //parent::__construct();
        //$this->setTemplate('rma/options.phtml');
    }
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset("js_form", array("legend"=>Mage::helper("rma")->__("General information")));
        
//        $fieldset->addField('attribute_code', 'text', array(
//        'label'     => Mage::helper('rma')->__('Attribute Code '),
//        'name'      => 'attribute_code',
//        'required'  => true,
//        'class' => 'required-entry',
//        ));
        
        $fieldset->addField('attribute_options', 'button', array(
        'label'     => Mage::helper('rma')->__('Options '),
        'name'      => 'options',
        'required'  => true,
        'class' => 'required-entry',
        ));
        

        if (Mage::getSingleton("adminhtml/session")->getAttributeData())
        {
            $form->setValues(Mage::getSingleton("adminhtml/session")->getAttributeData());
            Mage::getSingleton("adminhtml/session")->setAttributeData(null);
        } 
        elseif(Mage::registry("attribute_data")) {
            $form->setValues(Mage::registry("attribute_data")->getData());
        }


    return parent::_prepareForm();

    } 
    
    
}
