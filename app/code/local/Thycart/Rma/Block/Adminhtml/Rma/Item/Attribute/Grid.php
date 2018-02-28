<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class Thycart_Rma_Block_Adminhtml_Rma_Item_Attribute_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {   
        parent::__construct();
        $this->setId('RmaAttributeGrid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }
    
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('rma/rma_eav_attribute')->getCollection();                             
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    
    protected function _prepareColumns()
    {   
        $this->addColumn('attribute_code', array(
            'header'    => Mage::helper('rma')->__('Attribute Code'),
            'align'     =>'left',          
            'width'     => '50px',
            'sortable'  =>true,
            'index'     => 'attribute_code',
        ));

        $this->addColumn('scope', array(
            'header'    => Mage::helper('rma')->__('Scope'),
            'align'     =>'left',          
            'width'     => '50px',
            'index'     => 'scope',
            'type' => 'options',
            'options' => array(
                '2' => Mage::helper('rma')->__('Global'),
                '1' => Mage::helper('rma')->__('Website'),
                '0' => Mage::helper('rma')->__('Store View'),
              ),
            'align' => 'center',
        ));
        
        $this->addColumn('action',array(            
            'header'    => Mage::helper('rma')->__('Action'),
            'width'     => '50px',
            'type'      => 'action',
            'getter'     => 'getId',
            'actions'   => array(
                array(
                    'caption' => Mage::helper('rma')->__('View'),
                    'url'     => array(
                        'base'=>'*/*/edit'                           
                    ),
                    'field'   => 'id'
                )
            ),
            'filter'    => false,
            'sortable'  => false,
            'index'     => 'stores',
        ));
      
        return parent::_prepareColumns();
    }
    
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
    
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('id');
        $this->getMassactionBlock()->setUseSelectAll(true);
        $this->getMassactionBlock()->addItem('remove_message', array(
                        'label'=> Mage::helper('rma')->__('Remove RMA Attributes'),
                        'url'  => $this->getUrl('*/adminhtml_attribute/massRemove'),
                        'confirm' => Mage::helper('rma')->__('Are you sure?')
                ));
        return $this;
    }
    
}

