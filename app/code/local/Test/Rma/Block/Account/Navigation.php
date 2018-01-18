<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class Test_Rma_Block_Account_Navigation extends Mage_Customer_Block_Account_Navigation
{
 
     public function removeLinkByName($name) {
        unset($this->_links[$name]);
    }
}

