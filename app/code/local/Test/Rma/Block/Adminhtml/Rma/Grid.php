<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class Test_Rma_Block_Adminhtml_Rma_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
   public function __construct()
    {
        parent::__construct();
        $this->setId('id');
        $this->setDefaultSort('id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }
    
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('employee/employee')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    
    protected function _prepareColumns()
    {
        $this->addColumn('id', array(
          'header'    => Mage::helper('rma')->__('ID'),
          'align'     =>'right',
          'width'     => '10px',
          'index'     => 'id',
        ));
 
        $this->addColumn('name', array(
          'header'    => Mage::helper('rma')->__('Name'),
          'align'     =>'left',
          'index'     => 'name',
          'width'     => '50px',
        ));
           
        $this->addColumn('content', array(
            'header'    => Mage::helper('rma')->__('Description'),
            'width'     => '150px',
            'index'     => 'content',
        ));
        return parent::_prepareColumns();
    }
}
  

