<?php

class Thycart_Rma_Block_Adminhtml_Rma_Edit_Tab_Items_Grid_Column_Renderer_Textinput
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
   
    public function render(Varien_Object $row) {      
       $value = $row->getData($this->getColumn()->getIndex());
        if (!$row->getIsQtyDecimal() && !is_null($value)) {
            $value = intval($value);
        }
        $class = 'input-text ' . $this->getColumn()->getValidateClass();
        $html = '<input type="text" ';
        $html .= 'name="items[' . $row->getId() . '][' . $this->getColumn()->getId() . ']" ';
        $html .= 'value="' . $value . '" ';
        if ($row['item_status']=='canceled') {
            $html .= ' disabled="disabled" ';          
        }
//        $html .= 'class="' . $class . '" />';
        return $html;  
    }
    
    
     public function getAttributeIsDisabled($attribute)
    {
        

        switch ($attribute) {
            case 'qty_authorized':
                $enabledStatus = Thycart_Rma_Model_Rma_Source_Status::STATE_PENDING;
                break;
            case 'qty_returned':
                $enabledStatus = Thycart_Rma_Model_Rma_Source_Status::STATE_AUTHORIZED;
                break;
            case 'qty_approved':
                $enabledStatus = Thycart_Rma_Model_Rma_Source_Status::STATE_RECEIVED;
                break;
            default:
                return false;
                break;
        }

        if ($enabledStatus == $this->getSequenceStatus()){
            return false;
        } else {
            return true;
        }
    }
   
}
