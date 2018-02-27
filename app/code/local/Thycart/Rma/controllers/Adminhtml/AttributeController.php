<?php
class Thycart_Rma_Adminhtml_AttributeController extends Mage_Adminhtml_Controller_Action
{  
    protected function _isAllowed()
    {
        return true;
    } 
    public function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('sales/rma');
        return $this;
    }
    
    public  function indexAction()
    {
        $this->_title($this->__('Manage RMA Item Attribute'));
        $this->_initAction()
            ->renderLayout();
    }
    
    public function newAction()
    {
        $this->_forward('edit');
    }
    
    public function editAction() 
    {
        $attributeId = 0;
        if($this->getRequest()->getParam('id'))
        {
            $attributeId = $this->getRequest()->getParam('id');
        }
        $this->_title($this->__('Manage RMA Item Attributes'));
        try
        {
            $attributeObject = Mage::getModel('rma/rma_eav_attribute')->load($attributeId);           
        }
        catch (Exception $e)
        {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            return;
            exit();
        }
        if (!empty($attributeObject) || empty($attributeId))
        {
            Mage::register('attribute_data', $attributeObject);
            $this->loadLayout();
            $this->_setActiveMenu('sales');
    
            $this->_initAction()
                 ->_addContent($this->getLayout()->createBlock("rma/adminhtml_rma_item_attribute_edit"))
                 ->_addLeft($this->getLayout()->createBlock("rma/adminhtml_rma_item_attribute_edit_tabs"))
                 ->renderLayout();  
        }
        else
        {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('rma')->__('Attribute does not exists'));
            $this->_redirect('*/*/');
            exit();
        }
    }
    public function viewAction() 
    {
        $this->loadLayout();
        $output = $this->getLayout()->getBlock('rma_item_attribute_edit_tab_js')->setTemplate('rma/options.phtml')->toHtml();
        $this->getResponse()->setBody($output);

    }
    public function saveAction()
    {
        $post_data = $this->getRequest()->getPost();       
        $option = $this->getRequest()->getParam('option');
        $id = $this->getRequest()->getParam('id');
        if(empty($post_data))
        {
            Mage::getSingleton('core/session')->addError('Data not posted');
        }
        if ($post_data) 
        {
            try 
            {
                $model = Mage::getModel("rma/rma_eav_attribute");

                if($this->getRequest()->getParam('id')) 
                {
                    $model->load($this->getRequest()->getParam('id'));
                }        
                $model->addData(array("attribute_code"=>$post_data['attribute_code'],"is_required"=>$post_data['is_required'],"is_unique"=>$post_data['is_unique'],"scope"=>$post_data['scope']));
                $result = $model->save();
                if($result)
                { 
                    $optionModel = Mage::getModel("rma/rma_eav_attributeoption");
                    if($this->getRequest()->getParam('id'))
                    {
                        $optionModel->load($this->getRequest()->getParam('id'),'attribute_id');
                    }

                    foreach($option['order'] as $key => $value)
                    {
                        if(stristr($key,'option_'))
                        {
                            $optionModelobj = Mage::getModel("rma/rma_eav_attributeoption");
                            $optionModelobj->addData(array("attribute_id"=>$model->getId(),"value"=>$value));
                            $optionModelobj->save();              
                        }
                        else 
                        {
                            if(isset($option['delete'][$key]) && !empty($option['delete'][$key]))
                            {
                                $deleteOptionModel = Mage::getModel("rma/rma_eav_attributeoption");
                                $deleteOptionModel->setId($key);
                                $deleteOptionModel->delete();
                            }
                            else
                            {
                                $updateOptionModel = Mage::getModel("rma/rma_eav_attributeoption")->load($key);
                                if($updateOptionModel)
                                { 
                                    $updateOptionModel->addData(array("attribute_id"=>$model->getId(),"value"=>$value));
                                    $updateOptionModel->save();
                                }    
                            }
                        }

                    }

                }

            } 
            catch (Exception $e) 
            {
                Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
                $this->_redirect("*/*/edit", array("id" => $this->getRequest()->getParam("id")));
                return;
            }
        }
        $this->_redirect("*/*/");
    }
    
    public function deleteAction()
    {
        if( $this->getRequest()->getParam("id") > 0 ) 
        {
            try 
            {
                $model = Mage::getModel("rma/rma_eav_attribute");
                $model->setId($this->getRequest()->getParam("id"))->delete();
                Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("RMA Attribute was successfully deleted"));
                $this->_redirect("*/*/");
            } 
            catch (Exception $e) 
            {
                Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
                $this->_redirect("*/*/edit", array("id" => $this->getRequest()->getParam("id")));
            }
        }
        $this->_redirect("*/*/");
    }


    public function massRemoveAction()
    {
        try 
        {
            $ids = $this->getRequest()->getPost('id', array());
            foreach ($ids as $id) 
            {
                $model = Mage::getModel("rma/rma_eav_attribute");
                $model->setId($id)->delete();
            }
            Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("RMA Attributes was successfully removed"));
        }
        catch (Exception $e) 
        {
            Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
        }
        $this->_redirect('*/*/');
    }
}

