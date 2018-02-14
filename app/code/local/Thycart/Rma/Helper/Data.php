<?php 
class Thycart_Rma_Helper_Data extends Mage_Core_Helper_Abstract
{
     
    public function getAttributeOptionValues($attribute_code) {
        $attribute_data=Mage::getModel('rma/rma_eav_attribute')->getAttributeCollection();
        '<select name="resolution_type" class="validate-select resolution_type">
                    <option value="">-- Select Resolution --</option>
                    <?php foreach ($resolution as $key => $value) {?>        
                    <option value="<?php echo $key;?>"><?php echo $value;?></option>
                   <?php
                    }?>
                </select>';
        return $attribute_data[$attribute_code];
    }
    

}
?>
