<?php

class Thycart_Rma_Block_Adminhtml_Rma_Edit_Tab_Items_Grid_Column_Renderer_Textselect
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
   public function render(Varien_Object $row) {
        $disabled = ( ($row['item_status'] == Thycart_Rma_Model_Rma_Status::STATE_CANCELED && (!is_null($row['item_status']))) ? ' disabled="disabled"' : '' );       
        $selectName = 'items[' . $row->getId() . '][' . $this->getColumn()->getId() . ']';
        $html = '<select name="'. $selectName .'" class="action-select required-entry"'.$disabled.'>';
        $value = $row->getData($this->getColumn()->getIndex());
        $html.= '<option value=""></option>';
        foreach ($this->getColumn()->getOptions() as $val => $label){
            $selected = ( ($val == $value && (!is_null($value))) ? ' selected="selected"' : '' );
            $html.= '<option  value="' . $val . '"' . $selected .'>' . $label . '</option>';
        }
        $html.='</select>';
        return $html;       
    }
    
    
}
