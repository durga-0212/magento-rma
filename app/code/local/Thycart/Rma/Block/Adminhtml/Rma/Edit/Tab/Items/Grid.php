<?php
class Thycart_Rma_Block_Adminhtml_Rma_Edit_Tab_Items_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Default limit collection
     *
     * @var int
     */
    protected $_defaultLimit = 0;

    /**
     * Variable to store store-depended string values of attributes
     *
     * @var null|array
     */
    protected $_attributeOptionValues = null;

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
        //$this->_gatherOrderItemsData();
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
//        echo "=====================Rma===";
//        echo '<pre>';
//        print_r($collection->getData());
//        die;
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

        $this->addColumn('product_admin_name', array(
            'header' => Mage::helper('rma')->__('Product Name'),
            'width'  => '80px',
            'type'   => 'text',
            'index'  => 'product_name',
            'escape' => true,
        ));

        $this->addColumn('product_admin_sku', array(
            'header'=> Mage::helper('rma')->__('SKU'),
            'width' => '80px',
            'type'  => 'text',
            'index' => 'product_sku',
        ));
//
//        //Renderer puts available quantity instead of order_item_id
        $this->addColumn('qty_ordered', array(
            'header'=> Mage::helper('rma')->__('Remaining Qty'),
            'width' => '80px',           
            'index' => 'qty_ordered',
            'type'  => 'text'           
        ));
//
        $this->addColumn('qty_requested', array(
            'header'=> Mage::helper('rma')->__('Requested Qty'),
            'width' => '80px',
            'index' => 'qty_requested',            
            'validate_class' => 'validate-greater-than-zero'
        ));
        
         $this->addColumn('qty_approved', array(
            'header'=> Mage::helper('rma')->__('Approved Qty'),
            'width' => '80px',
            'index' => 'qty_approved',
            'renderer'  => 'rma/adminhtml_rma_edit_tab_items_grid_column_renderer_textinput',
            'validate_class' => 'validate-one-required'
        ));

        $this->addColumn('qty_returned', array(
            'header'=> Mage::helper('rma')->__('Returned Qty'),
            'width' => '80px',
            'index' => 'qty_returned',          
            'validate_class' => 'validate-greater-than-zero'
        ));       

        $this->addColumn('reason', array(
            'header'=> Mage::helper('rma')->__('Reason to Return'),
            'width' => '80px',
            'index' => 'reason',
        ));

        $this->addColumn('condition', array(
            'header'=> Mage::helper('rma')->__('Delivery Status'),
            'width' => '80px',
          //  'type' => 'options',          
           // 'renderer'  => 'rma/adminhtml_rma_edit_tab_items_grid_column_renderer_textselect',
            //'options' => Mage::helper('rma')->getAttributeOptionValues('delivery_status'),
            'index' => 'delivery_status',
        ));

        $this->addColumn('resolution', array(
            'header'=> Mage::helper('rma')->__('Resolution'),
            'width' => '80px',
            'index' => 'resolution', 
           // 'type' => 'options',
           // 'renderer'  => 'rma/adminhtml_rma_edit_tab_items_grid_column_renderer_textselect',
            // 'options' => Mage::helper('rma')->getAttributeOptionValues('resolution'),
        ));

        $this->addColumn('status', array(
            'header'=> Mage::helper('rma')->__('Status'),
            'width' => '80px',
            'type' => 'options',
            'index' => 'item_status',           
            'renderer'  => 'rma/adminhtml_rma_edit_tab_items_grid_column_renderer_textselect',
            'options' => Mage::helper('rma')->getAttributeOptionValues('item_status'),
        ));
        
        
        $this->addColumn('order_item_id', array(
            'header'=> Mage::helper('rma')->__('Order Item Id'),
            'width' => '80px',
            'index' => 'order_item_id',          
            'renderer'  => 'rma/adminhtml_rma_edit_tab_items_grid_column_renderer_textinput',
            'column_css_class'=>'no-display',
            'header_css_class'=>'no-display'   
        ));

        return parent::_prepareColumns();
    }

    /**
     * Get available for return item quantity
     *
     * @param Varien_Object $row
     * @return int
     */
    public function getQtyOrdered($row)
    {
        $orderItemsData = $this->getOrderItemsData();
        if (is_array($orderItemsData)
                && isset($orderItemsData[$row->getOrderItemId()])
                && isset($orderItemsData[$row->getOrderItemId()]['qty_shipped'])
                && isset($orderItemsData[$row->getOrderItemId()]['qty_returned'])) {
            $return = $orderItemsData[$row->getOrderItemId()]['qty_shipped'] -
                    $orderItemsData[$row->getOrderItemId()]['qty_returned'];
        } else {
            $return = 0;
        }
        return $return;
    }

    /**
     * Get string value of "Reason to Return" Attribute
     *
     * @param Varien_Object $row
     * @return string
     */
    public function getReasonOptionStringValue($row)
    {
        return $this->_getAttributeOptionStringValue($row->getReason());
    }

    /**
     * Get string value of "Reason to Return" Attribute
     *
     * @param Varien_Object $row
     * @return string
     */
    public function getResolutionOptionStringValue($row)
    {
        return $this->_getAttributeOptionStringValue($row->getResolution());
    }

    /**
     * Get string value of "Reason to Return" Attribute
     *
     * @param Varien_Object $row
     * @return string
     */
    public function getConditionOptionStringValue($row)
    {
        return $this->_getAttributeOptionStringValue($row->getCondition());
    }

    /**
     * Get string value of "Status" Attribute
     *
     * @param Varien_Object $row
     * @return string
     */
    public function getStatusOptionStringValue($row)
    {
        return $row->getStatusLabel();
    }

    /**
     * Get string value option-type attribute by it's unique int value
     *
     * @param int $value
     * @return string
     */
    protected function _getAttributeOptionStringValue($value)
    {
        if (is_null($this->_attributeOptionValues)) {
            $this->_attributeOptionValues = Mage::helper('rma/eav')->getAttributeOptionStringValues();
        }
        if (isset($this->_attributeOptionValues[$value])) {
            return $this->escapeHtml($this->_attributeOptionValues[$value]);
        } else {
            return $this->escapeHtml($value);
        }
    }

    /**
     * Sets all available fields in editable state
     *
     * @return Enterprise_Rma_Block_Adminhtml_Rma_Edit_Tab_Items_Grid
     */
    public function setAllFieldsEditable()
    {
        Mage::getSingleton('rma/item_status')->setAllEditable();
        return $this;
    }
    
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('id');
        $this->getMassactionBlock()->setUseSelectAll(true);
        $this->getMassactionBlock()->addItem('pending', array(
                         'label'=> Mage::helper('rma')->__('Pending'),
                         'url'  => $this->getUrl('*/adminhtml_rma/save'),
                         'confirm' => Mage::helper('rma')->__('Are you sure?')
                ));
         
          $this->getMassactionBlock()->addItem('processing', array(
                         'label'=> Mage::helper('rma')->__('Processing'),
                         'url'  => $this->getUrl('*/adminhtml_rma/save'),
                         'confirm' => Mage::helper('rma')->__('Are you sure?')
                ));
           $this->getMassactionBlock()->addItem('approved', array(
                         'label'=> Mage::helper('rma')->__('Return Received'),
                         'url'  => $this->getUrl('*/adminhtml_rma/save'),
                         'confirm' => Mage::helper('rma')->__('Are you sure?')
                ));
            $this->getMassactionBlock()->addItem('rejected', array(
                         'label'=> Mage::helper('rma')->__('Complete'),
                         'url'  => $this->getUrl('*/adminhtml_rma/save'),
                         'confirm' => Mage::helper('rma')->__('Are you sure?')
                ));
        return $this;
    }

}
