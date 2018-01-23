<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class Test_Rma_Block_Adminhtml_Product_Grid extends Mage_Adminhtml_Block_Catalog_Product_Grid
{   
    protected function _prepareColumns() { 
        $this->addExportType('rma/adminhtml_rma/exportCsv', Mage::helper('rma')->__('CSV')); 
	$this->addExportType('rma/adminhtml_rma/exportExcel', Mage::helper('rma')->__('Excel'));
        return parent::_prepareColumns();
    }

}

