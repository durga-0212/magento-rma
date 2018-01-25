<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class Thycart_Rma_Block_Account_Navigation extends Mage_Customer_Block_Account_Navigation
{
    
    public function addDashboardLink($name,$path,$label) {
        $result = Mage::getStoreConfig('rma_section/rma_group/rma_field');
        if($result)
        {
            $this->addLink($name, $path, $label); 
        }        
    }
}

