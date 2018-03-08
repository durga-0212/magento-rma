<?php

class Thycart_Rma_Block_Adminhtml_Rma_Edit_Tab_Items_Grid_Column_Renderer_Textinput
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
   
    public function render(Varien_Object $row) 
    {   
        $html = '';
        if($row->getEntityId() == 'Totals')
        {
            return $html;
        }
        $value = $row->getData($this->getColumn()->getIndex());  
        if (!$row->getIsQtyDecimal() && !is_null($value)) {
            $value = intval($value);
        }
        $class = 'input-text ' . $this->getColumn()->getValidateClass();
        $html = '<input type="text" class="validate-not-negative-number validate-no-html-tags validate-greater-than-zero validate-digits validate-number" id="qtyApproved"';
        if($row['item_status']!= Thycart_Rma_Model_Rma_Status::STATE_PENDING)
        {
            $html.=' readonly ';
        }
        $html .= 'name="items[' . $row->getId() . '][' . $this->getColumn()->getId() . ']" ';
        $html .= 'value="' . $value . '" ';
        if ($row['item_status']==Thycart_Rma_Model_Rma_Status::STATE_CANCELED)
        {
            $html .= ' disabled="disabled" ';          
        }
        return $html;  
    }
   
}
