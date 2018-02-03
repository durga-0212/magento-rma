<?php 
class Thycart_Rma_Block_Adminhtml_Rma_Item_Attribute_Edit
    extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Return current customer address attribute instance
     *
     * @return Mage_Rma_Model_Item_Attribute
     */
    protected function _getAttribute()
    {
        return Mage::registry('entity_attribute');
    }

    /**
     * Initialize Customer Address Attribute Edit Container
     *
     */
    public function __construct()
    {
        $this->_objectId    = 'attribute_id';
        $this->_blockGroup  = 'rma';
        $this->_controller  = 'adminhtml_rma_item_attribute';

        parent::__construct();

        $this->_addButton(
            'save_and_edit_button',
            array(
                'label'     => Mage::helper('rma')->__('Save and Continue Edit'),
                'onclick'   => 'saveAndContinueEdit()',
                'class'     => 'save'
            ),
            100
        );

        $this->_updateButton('save', 'label', Mage::helper('rma')->__('Save Attribute'));

        if (!$this->_getAttribute()->getIsUserDefined()) {
            $this->_removeButton('delete');
        } else {
            $this->_updateButton('delete', 'label', Mage::helper('rma')->__('Delete Attribute'));
        }
    }

    /**
     * Return header text for edit block
     *
     * @return string
     */
    public function getHeaderText()
    {
//        if ($this->_getAttribute()->getId()) {
//            $label = $this->_getAttribute()->getFrontendLabel();
//            if (is_array($label)) {
//                // restored label
//                $label = $label[0];
//            }
            return Mage::helper('rma')->__('Edit RMA Item Attribute "%s"', $label);
        //} else {
//            return Mage::helper('rma')->__('New RMA Item Attribute');
//        }
    }

    /**
     * Return validation url for edit form
     *
     * @return string
     */
    public function getValidationUrl()
    {
        return $this->getUrl('*/*/validate', array('_current' => true));
    }

    /**
     * Return save url for edit form
     *
     * @return string
     */
    public function getSaveUrl()
    {
        return $this->getUrl('*/*/save', array('_current' => true, 'back' => null));
    }
}

