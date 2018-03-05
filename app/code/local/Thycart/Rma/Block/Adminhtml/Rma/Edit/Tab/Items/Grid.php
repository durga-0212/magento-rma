<?php
class Thycart_Rma_Block_Adminhtml_Rma_Edit_Tab_Items_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    

    /**
     * Block constructor
     */
    public function _construct()
    {
        parent::_construct();
        $this->setId('rma_item_edit_grid');
        $this->setDefaultSort('entity_id');
        $this->setPagerVisibility(false);
        $this->setFilterVisibility(false);
        $this->setSortable(false);
    }

    protected function _prepareCollection()
    {
        $rmaData = Mage::registry('rma_data');
        
        $collection = Mage::getModel('rma/order')->getCollection()                       
            ->join(array('ra'=>'rma/rma_attributes'),'main_table.entity_id=ra.rma_entity_id')
            ->addFieldToSelect('*')  
                ->join(array('ri'=>'rma/rma_item'),'main_table.entity_id=ri.rma_entity_id')
            ->addFieldToFilter('main_table.entity_id',$rmaData->getEntityId());
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Prepare columns
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareColumns()
    {
        $rma = Mage::registry('rma_data');
        $rmaItemModel = Mage::getModel('rma/rma_item')->load($rma->getEntityId(),'rma_entity_id');
        $status = $rmaItemModel->getItemStatus();
        
        $this->addColumn('product_name', array(
            'header' => Mage::helper('rma')->__('Product Name'),
            'width'  => '80px',
            'type'   => 'text',
            'index'  => 'product_name',
            'escape' => true,
        ));

        $this->addColumn('product_sku', array(
            'header'=> Mage::helper('rma')->__('SKU'),
            'width' => '20px',
            'type'  => 'text',
            'index' => 'product_sku',
        ));

        $this->addColumn('qty_ordered', array(
            'header'=> Mage::helper('rma')->__('Ordered Qty'),
            'width' => '10px',           
            'index' => 'qty_ordered',
            'type'  => 'text'           
        ));

        $this->addColumn('qty_requested', array(
            'header'=> Mage::helper('rma')->__('Requested Qty'),
            'width' => '10px',
            'index' => 'qty_requested',            
            'validate_class' => 'validate-greater-than-zero'
        ));
        
        if($rma['status']== Thycart_Rma_Model_Rma_Status::STATE_CANCELED)
        {
            $this->addColumn('qty_canceled', array(
                'header'=> Mage::helper('rma')->__('Canceled Qty'),
                'width' => '10px',
                'index' => 'qty_requested',            
                'validate_class' => 'validate-greater-than-zero'
            ));
        }
        elseif($status == Thycart_Rma_Model_Rma_Status::STATE_PENDING)
        {
            $this->addColumn('qty_approved', array(
                'header'=> Mage::helper('rma')->__('Approved Qty'),
                'width' => '10px',
                'index' => 'qty_requested',
                'renderer'  => 'rma/adminhtml_rma_edit_tab_items_grid_column_renderer_textinput',
            ));
        }
        else
        {
            $this->addColumn('qty_approved', array(
                'header'=> Mage::helper('rma')->__('Approved Qty'),
                'width' => '10px',
                'index' => 'qty_approved',
                'renderer'  => 'rma/adminhtml_rma_edit_tab_items_grid_column_renderer_textinput',
            ));  
        }    

        $this->addColumn('reason', array(
            'header'=> Mage::helper('rma')->__('Reason to Return'),
            'width' => '80px',
            'index' => 'reason',
        ));

        $this->addColumn('delivery_status', array(
            'header'=> Mage::helper('rma')->__('Delivery Status'),
            'width' => '80px',          
            'index' => 'delivery_status',
        ));

        $this->addColumn('resolution', array(
            'header'=> Mage::helper('rma')->__('Resolution'),
            'width' => '80px',
            'index' => 'resolution', 
        ));

        $this->addColumn('status', array(
            'header'=> Mage::helper('rma')->__('Status'),
            'width' => '80px',
            'type' => 'options',
            'index' => 'item_status',           
            'renderer'  => 'rma/adminhtml_rma_edit_tab_items_grid_column_renderer_textselect',
            'options' => Mage::helper('rma')->getAttributeOptionValues('item_status'),
        )); 
        
        return parent::_prepareColumns();
    }

}
