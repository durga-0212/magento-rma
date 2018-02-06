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
                      //->join(array('rma' => 'rma/rma_eav_attributeoption'), 'main_table.attribute_id = rma.attribute_id');       
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    
    protected function _prepareColumns()
    {   
        $this->addColumn('attribute_id', array(
          'header'    => Mage::helper('rma')->__('Attribute ID'),
          'align'     =>'right',
          'width'     => '10px',
          'index'     => 'attribute_id',
        ));
        
        $this->addColumn('attribute_code', array(
          'header'    => Mage::helper('rma')->__('Attribute Code'),
          'align'     =>'left',          
          'width'     => '50px',
          'index'     => 'attribute_code',
        ));
        
        $this->addColumn('is_required', array(
          'header'    => Mage::helper('rma')->__('Is Required'),
          'align'     =>'left',          
          'width'     => '50px',
          'index'     => 'is_required',
        ));
          
        $this->addColumn('is_unique', array(
            'header'    => Mage::helper('rma')->__('Is Unique'),
            'width'     => '150px',
            'align'     =>'content',
            'index'     =>'is_unique',
        ));
        
//        $this->addColumn('entity_id', array(
//            'header'    => Mage::helper('rma')->__('Entity Id'),
//            'width'     => '150px',
//            'align'     =>'content',
//            'index'     =>'entity_id',
//        ));
//        
//        $this->addColumn('value', array(
//            'header'    => Mage::helper('rma')->__('Value'),
//            'width'     => '150px',
//            'align'     =>'content',
//            'index'     =>'value',
//        ));
        
         $this->addColumn('action',
            array(
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
        
        $this->addExportType('*/*/exportCsv', Mage::helper('rma')->__('CSV')); 
	$this->addExportType('*/*/exportExcel', Mage::helper('rma')->__('Excel'));
        return parent::_prepareColumns();
    }
    
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
    
    
    
}

