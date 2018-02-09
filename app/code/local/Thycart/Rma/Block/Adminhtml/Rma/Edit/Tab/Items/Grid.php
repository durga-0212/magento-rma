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

    /**
     * Gather items quantity data from Order item collection
     *
     * @return void
     */
//    protected function _gatherOrderItemsData()
//    {
//        $itemsData = array();
//        foreach (Mage::registry('current_order')->getItemsCollection() as $item) {
//            $itemsData[$item->getId()] = array(
//                'qty_shipped' => $item->getQtyShipped(),
//                'qty_returned' => $item->getQtyReturned()
//            );
//        }
//        $this->setOrderItemsData($itemsData);
//    }

    /**
     * Prepare grid collection object
     *
     * @return Enterprise_Rma_Block_Adminhtml_Rma_Edit_Tab_Items_Grid
     */
    protected function _prepareCollection()
    {
        $rmaData = Mage::registry('rma_data');
        
        $collection = Mage::getModel('rma/rma_item')->getCollection()
            ->addFieldToSelect('*')
//            ->join(array('ra'=>'rma/rma_attributes'),'main_table.rma_entity_id=ra.rma_entity_id')
//            ->addFieldToSelect('*')
            ->addFieldToFilter('main_table.rma_entity_id',$rmaData->getEntityId());
//        
//        $collection = Mage::getModel('rma/rma_attributes')->getCollection()
//                ->addFieldToSelect('*')
//                 ->addFieldToFilter('main_table.rma_entity_id',$rmaData->getEntityId());
////            
       // $collection['reason']='text here';
       //echo '=====================RMA';
       // echo '<pre>';
       // print_r($collection->getData()); die;
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
        //$statusManager = Mage::getSingleton('rma/rma_item');
        $rma = Mage::registry('rma_data');
//        if ($rma
//            && (($rma->getStatus() === Enterprise_Rma_Model_Rma_Source_Status::STATE_CLOSED)
//                || ($rma->getStatus() === Enterprise_Rma_Model_Rma_Source_Status::STATE_PROCESSED_CLOSED))
//        ) {
//            $statusManager->setOrderIsClosed();
//        }

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
            //'getter'   => array($this, 'getQtyOrdered'),
            //'renderer'  => 'rma/adminhtml_rma_edit_tab_items_grid_column_renderer_quantity',
            'index' => 'qty_ordered',
            'type'  => 'text',
            //'order_data' => $this->getOrderItemsData(),
        ));
//
        $this->addColumn('qty_requested', array(
            'header'=> Mage::helper('rma')->__('Requested Qty'),
            'width' => '80px',
            'index' => 'qty_requested',
            //'renderer'  => 'rma/adminhtml_rma_edit_tab_items_grid_column_renderer_textinput',
            'validate_class' => 'validate-greater-than-zero'
        ));
//
        $this->addColumn('qty_authorized', array(
            'header'=> Mage::helper('rma')->__('Authorized Qty'),
            'width' => '80px',
            'index' => 'qty_authorized',
            //'renderer'  => 'rma/adminhtml_rma_edit_tab_items_grid_column_renderer_textinput',
            'validate_class' => 'validate-greater-than-zero'
        ));

        $this->addColumn('qty_returned', array(
            'header'=> Mage::helper('rma')->__('Returned Qty'),
            'width' => '80px',
            'index' => 'qty_returned',
            //'renderer'  => 'rma/adminhtml_rma_edit_tab_items_grid_column_renderer_textinput',
            'validate_class' => 'validate-greater-than-zero'
        ));
//
        $this->addColumn('qty_approved', array(
            'header'=> Mage::helper('rma')->__('Approved Qty'),
            'width' => '80px',
            'index' => 'qty_approved',
            //'renderer'  => 'rma/adminhtml_rma_edit_tab_items_grid_column_renderer_textinput',
            'validate_class' => 'validate-greater-than-zero'
        ));

        $this->addColumn('reason', array(
            'header'=> Mage::helper('rma')->__('Reason to Return'),
            'width' => '80px',
//            'index' =>
            //'getter'   => array($this, 'getReasonOptionStringValue'),
            //'renderer'  => 'rma/adminhtml_rma_edit_tab_items_grid_column_renderer_reasonselect',
//            'options' => Mage::helper('rma')->getAttributeOptionValues('reason'),
            'index' => 'reason',
        ));
//
        $this->addColumn('condition', array(
            'header'=> Mage::helper('rma')->__('Item Condition'),
            'width' => '80px',
           // 'getter'   => array($this, 'getConditionOptionStringValue'),
            'renderer'  => 'rma/adminhtml_rma_edit_tab_items_grid_column_renderer_textselect',
            'options' => Mage::helper('rma')->getAttributeOptionValues('condition'),
            'index' => 'condition',
        ));

        $this->addColumn('resolution', array(
            'header'=> Mage::helper('rma')->__('Resolution'),
            'width' => '80px',
            'index' => 'resolution',           
//            'getter'   => array($this, 'getResolutionOptionStringValue'),
            'renderer'  => 'rma/adminhtml_rma_edit_tab_items_grid_column_renderer_textselect',
             'options' => Mage::helper('rma')->getAttributeOptionValues('resolution'),
        ));
//
        $this->addColumn('status', array(
            'header'=> Mage::helper('rma')->__('Status'),
            'width' => '80px',
            'index' => 'status',
            //'getter'=> array($this, 'getStatusOptionStringValue'),
            'renderer'  => 'rma/adminhtml_rma_edit_tab_items_grid_column_renderer_textselect',
            'options' => Mage::helper('rma')->getAttributeOptionValues('status'),
        ));
//
//        $actionsArray = array(
//            array(
//                'caption'   => Mage::helper('rma')->__('Details'),
//                'class'     => 'item_details',
//            ),
//        );
//        if (!($rma
//            && (($rma->getStatus() === Enterprise_Rma_Model_Rma_Source_Status::STATE_CLOSED)
//                || ($rma->getStatus() === Enterprise_Rma_Model_Rma_Source_Status::STATE_PROCESSED_CLOSED))
//        )) {
//                $actionsArray[] = array(
//                'caption'   => Mage::helper('rma')->__('Split'),
//                'class'     => 'item_split_line',
//                'status_depended' => '1'
//            );
//        }

        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('rma')->__('Action'),
                'width'     => '100',
                //'renderer'  => 'rma/adminhtml_rma_edit_tab_items_grid_column_renderer_action',
                //'actions'   => $actionsArray,
                'is_system' => true,
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
                         'url'  => $this->getUrl('*/adminhtml_rma/massChangeStatus'),
                         'confirm' => Mage::helper('rma')->__('Are you sure?')
                ));
          $this->getMassactionBlock()->addItem('partial_pending', array(
                         'label'=> Mage::helper('rma')->__('Partial Pending'),
                         'url'  => $this->getUrl('*/adminhtml_rma/massChangeStatus'),
                         'confirm' => Mage::helper('rma')->__('Are you sure?')
                ));
          $this->getMassactionBlock()->addItem('processed', array(
                         'label'=> Mage::helper('rma')->__('Processed'),
                         'url'  => $this->getUrl('*/adminhtml_rma/massChangeStatus'),
                         'confirm' => Mage::helper('rma')->__('Are you sure?')
                ));
           $this->getMassactionBlock()->addItem('approved', array(
                         'label'=> Mage::helper('rma')->__('Approved'),
                         'url'  => $this->getUrl('*/adminhtml_rma/massChangeStatus'),
                         'confirm' => Mage::helper('rma')->__('Are you sure?')
                ));
            $this->getMassactionBlock()->addItem('cancel', array(
                         'label'=> Mage::helper('rma')->__('Cancel'),
                         'url'  => $this->getUrl('*/adminhtml_rma/massChangeStatus'),
                         'confirm' => Mage::helper('rma')->__('Are you sure?')
                ));
        return $this;
    }

}
