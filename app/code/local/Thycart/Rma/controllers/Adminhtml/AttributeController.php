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
             ->_setActiveMenu('sales/rma');
//             ->_addBreadcrumb(Mage::helper('rma')->__('RMA'))
//             ->_addBreadcrumb(Mage::helper('rma')->__('Manage RMA Item Attribute'));
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
        $attributeId = $this->getRequest()->getParam('id');
        $attributeObject = $this->_initAttribute();
            //->setEntityTypeId($this->_getEntityType()->getId());

        $this->_title($this->__('Manage RMA Item Attributes'));
        Mage::register('attribute_data', $attributeObject);
        if ($attributeId) {
            $attributeObject->load($attributeId);
//            if (!$attributeObject->getId()) {
//                $this->_getSession()
//                    ->addError(Mage::helper('rma')->__('Attribute is no longer exists.'));
//                $this->_redirect('*/*/');
//                return;
//            }
//            if ($attributeObject->getEntityTypeId() != $this->_getEntityType()->getId()) {
//                $this->_getSession()->addError(Mage::helper('rma')->__('You cannot edit this attribute.'));
//                $this->_redirect('*/*/');
//                return;
//            }

            //$this->_title($attributeObject->getFrontendLabel());
        } else {
            $this->_title($this->__('New Attribute'));
            $label = Mage::helper('rma')->__('Add RMA Item Attribute');
        }
        
        //$attributeData = $this->_getSession()->getAttributeData(true);
        
        if (!empty($attributeData)) { 
            $attributeObject->setData($attributeData);
        }
        Mage::register('entity_attribute', $attributeObject);

//        $label = $attributeObject->getId()
//            ? Mage::helper('rma')->__('Edit RMA Item Attribute')
//            : Mage::helper('rma')->__('New RMA Item Attribute');

        $this->_initAction()
            ->_addBreadcrumb($label, $label)
            ->_addContent($this->getLayout()->createBlock("rma/adminhtml_rma_item_attribute_edit"))
            ->_addLeft($this->getLayout()->createBlock("rma/adminhtml_rma_item_attribute_edit_tabs"))
            ->renderLayout();
        //Zend_Debug::dump($this->getLayout()->getUpdate()->getHandles());        
    }
    
    public function _initAttribute()
    {
        $attribute = Mage::getModel('rma/rma_eav_attribute');//->getCollection();
                     //->join(array('rma' => 'rma/rma_eav_attributeoption'), 'main_table.attribute_id = rma.attribute_id');
        $websiteId = $this->getRequest()->getParam('website');
        if ($websiteId) {
            $attribute->setWebsite($websiteId);
        }
        return $attribute;
    }
    
    
    public function viewAction() {
        $this->loadLayout();
        echo $this->getLayout()->getBlock('rma_item_attribute_edit_tab_js')->setTemplate('rma/options.phtml')->toHtml();
//        $this->renderLayout();
        //Zend_Debug::dump($this->getLayout()->getUpdate()->getHandles());
    }
    public function saveAction()
    {
        $post_data=$this->getRequest()->getPost();
	//print_r($post_data);die;
        $id = $this->getRequest()->getParam('id');
        if(empty($post_data))
        {
            Mage::getSingleton('core/session')->addError('Data not posted');
        }
        if ($post_data) 
        {
                try {
                        $model = Mage::getModel("rma/rma_eav_attribute");

                        if($this->getRequest()->getParam('id')) 
                        {

                                $model->load($this->getRequest()->getParam('id'));
                        }        
            $model->setData(array("attribute_code"=>$post_data['attribute_code'],"is_required"=>1,"is_unique"=>1));
            //$model->save();
            
            if($model->save())
            { //echo "in";die;
                $optionModel = Mage::getModel("rma/rma_eav_attributeoption");
                if($this->getRequest()->getParam('id'))
                {
                    $optionModel->load($this->getRequest()->getParam('id'),'attribute_id');
                }
                //echo '<pre>';print_r($optionModel);die;
                $optionModel->setData(array("attribute_id"=>$model->getId(),"value"=>$post_data['options']));
                $optionModel->save();
            }


                    } 
                    catch (Exception $e) 
                    {
                            Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
                            Mage::getSingleton("adminhtml/session")->setMessageData($this->getRequest()->getPost());
                            $this->_redirect("*/*/edit", array("id" => $this->getRequest()->getParam("id")));
                    return;
                    }

        }
                $this->_redirect("*/*/");
        
    }
}

