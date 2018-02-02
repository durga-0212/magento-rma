<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class Thycart_Rma_Adminhtml_AttributeController extends Mage_Adminhtml_Controller_Action
{
    public  function _initAction()
    {
        $this->loadLayout()
             ->_setActiveMenu('sales/rma')
             ->_addBreadcrumb(
                Mage::helper('rma')->__('RMA'))
             ->_addBreadcrumb(
                Mage::helper('rma')->__('Manage RMA Item Attribute'));
        return $this;
    }
    
    public  function indexAction()
    {
        $this->_title($this->__('Manage RMA Item Attribute'));
        $this->_initAction()
             ->renderLayout();
        //Zend_Debug::dump($this->getLayout()->getUpdate()->getHandles());
    }
    
    public function newAction()
    {
        $this->addActionLayoutHandles();
        $this->_forward('edit');
    }
    
    public function editAction() 
    {
        $attributeId = $this->getRequest()->getParam('attribute_id');
        $attributeObject = $this->_initAttribute();
            //->setEntityTypeId($this->_getEntityType()->getId());

        $this->_title($this->__('Manage RMA Item Attributes'));

        if ($attributeId) {
            $attributeObject->load($attributeId);
            if (!$attributeObject->getId()) {
                $this->_getSession()
                    ->addError(Mage::helper('rma')->__('Attribute is no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }
            if ($attributeObject->getEntityTypeId() != $this->_getEntityType()->getId()) {
                $this->_getSession()->addError(Mage::helper('rma')->__('You cannot edit this attribute.'));
                $this->_redirect('*/*/');
                return;
            }

            $this->_title($attributeObject->getFrontendLabel());
        } else {
            $this->_title($this->__('New Attribute'));
            $label = Mage::helper('rma')->__('Edit RMA Item Attribute');
        }
        
        $attributeData = $this->_getSession()->getAttributeData(true);
        
        if (!empty($attributeData)) { 
            $attributeObject->setData($attributeData);
        }
        Mage::register('entity_attribute', $attributeObject);

//        $label = $attributeObject->getId()
//            ? Mage::helper('rma')->__('Edit RMA Item Attribute')
//            : Mage::helper('rma')->__('New RMA Item Attribute');

        $this->_initAction()
            ->_addBreadcrumb($label, $label)
            ->renderLayout();
        Zend_Debug::dump($this->getLayout()->getUpdate()->getHandles());        
    }
    
    public function _initAttribute()
    {
        $attribute = Mage::getModel('rma/item_attribute');
        $websiteId = $this->getRequest()->getParam('website');
        if ($websiteId) {
            $attribute->setWebsite($websiteId);
        }
        return $attribute;
    }
}

