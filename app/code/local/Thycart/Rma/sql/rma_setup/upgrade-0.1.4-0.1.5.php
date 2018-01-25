<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$installer = $this;
 
$installer->startSetup();
$tableName = $installer->getTable('rma_order_item');
 
$table = $installer->getConnection()
        ->addColumn(
        $tableName,
        'qty_ordered',
        array(
            'TYPE'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
            'SCALE'     => 4,
            'PRECISION' => 12,
            'DEFAULT'   => '0.0000',
            'NULLABLE'  => false,
             'AFTER'     => 'is_qty_decimal', 
            'COMMENT'   => 'Qty of ordered items',
        )
       );  
$installer->endSetup();