<?php

class Thycart_Rma_Block_Adminhtml_Rma_Edit_Tab_Items_Grid_Column_Renderer_Textselect
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
   public function render(Varien_Object $row)
   {
        $disabled = ( ($row['item_status'] == Thycart_Rma_Model_Rma_Status::STATE_CANCELED && (!is_null($row['item_status']))) ? ' disabled="disabled"' : '' );       
        $selectName = 'items[' . $row->getId() . '][' . $this->getColumn()->getId() . ']';
        $html = '<select name="'. $selectName .'" class="action-select required-entry"'.$disabled.'>';
        $value = $row->getData($this->getColumn()->getIndex());
        $html.= '<option value=""></option>';
        $statusArray = $this->getColumn()->getOptions();
        $priorityArray = array_values($statusArray);
        $priorityArray =array_reverse($priorityArray);
        for ($i=0; $i < count($priorityArray); $i++)
        { 
            $status = strtolower($priorityArray[$i]);
            if($status == $value)
            {
                $selected = ($status == $value && (!is_null($value)) ? ' selected="selected"' : '' );
                if($value != 'canceled')
                {
                    $html.= '<option  value="' . strtolower($priorityArray[$i+1]) . '"' . $selected .'>' . $priorityArray[$i+1] . '</option>';
                    if($value == 'processing')
                    {
                        $html.= '<option  value="' . strtolower(end($priorityArray)) . '"' . $selected .'>' . strtolower(end($priorityArray)) . '</option>';
                    }
                }
                $html.= '<option  value="' . $status . '"' . $selected .'>' . $status . '</option>';
            }   
        }
        $html.='</select>';
        return $html;       
    }    
}
