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
    
//    protected function _prepareCollection()
//    {
//        $collection = Mage::getModel('rma/order')->getCollection();       
//        $this->setCollection($collection);
//        return parent::_prepareCollection();
//    }
//    
//    protected function _prepareColumns()
//    {   
//        $this->addColumn('id', array(
//          'header'    => Mage::helper('rma')->__('ID'),
//          'align'     =>'right',
//          'width'     => '10px',
//          'index'     => 'entity_id',
//        ));
//        
//        $this->addColumn('order_date', array(
//          'header'    => Mage::helper('rma')->__('Order Date'),
//          'align'     =>'left',
//          'index'     => 'order_date',
//          'width'     => '50px',
//        ));
//        
//        $this->addColumn('date_requested', array(
//          'header'    => Mage::helper('rma')->__('Requested Date'),
//          'align'     =>'left',
//          'index'     => 'date_requested',
//          'width'     => '50px',
//        ));
//          
//        $this->addColumn('customer_name', array(
//            'header'    => Mage::helper('rma')->__('Customer Name'),
//            'width'     => '150px',
//            'align'     =>'content',
//            'index'     =>'customer_name',
//        ));
//        
//        $this->addColumn('status', array(
//            'header'    => Mage::helper('rma')->__('Status'),
//            'width'     => '150px',
//            'align'     =>'content',
//            'index'     =>'status',
//        ));
//        
//         $this->addColumn('action',
//            array(
//                'header'    => Mage::helper('rma')->__('Action'),
//                'width'     => '50px',
//                'type'      => 'action',
//                'getter'     => 'getId',
//                'actions'   => array(
//                    array(
//                        'caption' => Mage::helper('rma')->__('View'),
//                        'url'     => array(
//                            'base'=>'*/*/edit'                           
//                        ),
//                        'field'   => 'id'
//                    )
//                ),
//                'filter'    => false,
//                'sortable'  => false,
//                'index'     => 'stores',
//        ));
//        
//        $this->addExportType('*/*/exportCsv', Mage::helper('rma')->__('CSV')); 
//	$this->addExportType('*/*/exportExcel', Mage::helper('rma')->__('Excel'));
//        return parent::_prepareColumns();
//    }
//    
//    public function getRowUrl($row)
//    {
//        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
//    }
//    
    
}

