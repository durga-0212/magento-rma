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
        $this->setId('RmaGrid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }
    
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('rma/rma_order')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    
    protected function _prepareColumns()
    {   
        $this->addColumn('entity_id', array(
          'header'    => Mage::helper('rma')->__('ID'),
          'align'     =>'right',
          'width'     => '10px',
          'index'     => 'entity_id',
        ));
        
        $this->addColumn('order_date', array(
          'header'    => Mage::helper('rma')->__('Order Date'),
          'align'     =>'left',
          'index'     => 'order_date',
          'width'     => '50px',
        ));
        
        $this->addColumn('date_requested', array(
          'header'    => Mage::helper('rma')->__('Requested Date'),
          'align'     =>'left',
          'index'     => 'date_requested',
          'width'     => '50px',
        ));
          
        $this->addColumn('customer_name', array(
            'header'    => Mage::helper('rma')->__('Customer Name'),
            'width'     => '150px',
            'align'     =>'content',
            'index'     => 'customer_name',
        ));
        return parent::_prepareColumns();
    }
}