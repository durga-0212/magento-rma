<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class Thycart_Rma_Block_Account_Navigation extends Mage_Core_Block_Template
{     
    public function addDashboardLink($name,$path,$label) 
    { 
        $moduleName = 'Thycart_Rma'; // edit to your required module name
        if (Mage::helper('core')->isModuleEnabled($moduleName)) 
        {
            if(Mage::getStoreConfig('rma_section/rma_group/rma_field'))
            {
                $block = Mage::app()->getLayout()->getBlock('customer_account_navigation');     
                $block->addlink($name, $path, $label);
            }
            else
            {              
                if(Mage::app()->getRequest()->getModuleName()=='rma')
                {
                    $url = Mage::getBaseUrl().'customer/account/';
                    Mage::app()->getResponse()->setRedirect($url);
                }
            }
        }
    }
}

