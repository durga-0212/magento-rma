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
        'rma_entity_id',
        array(
            'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
            'length'     => 11,
            'unsigned' => true,
            'nullable'  => false,            
            'COMMENT'   => 'Rma Entity Id',
        )
       )
       ->addColumn(
               $tableName,
             'is_qty_decimal', 
             array(
             'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,            
              'comment'   => 'Is item quantity decimal',
         )
             )
       ->addColumn(
        $tableName,
        'qty_requested',
        array(
            'TYPE'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
            'SCALE'     => 4,
            'PRECISION' => 12,
            'DEFAULT'   => '0.0000',
            'NULLABLE'  => false,
            'COMMENT'   => 'Qty of requested items',
        )
       )
        ->addColumn(
        $tableName,
        'qty_requested',
        array(
            'TYPE'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
            'SCALE'     => 4,
            'PRECISION' => 12,
            'DEFAULT'   => '0.0000',
            'NULLABLE'  => false,
            'COMMENT'   => 'Qty of authorized items',
        )
       )
        ->addColumn(
        $tableName,
        'qty_approved',
        array(
            'TYPE'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
            'SCALE'     => 4,
            'PRECISION' => 12,
            'DEFAULT'   => '0.0000',
            'NULLABLE'  => false,
            'COMMENT'   => 'Qty of approved items',
        )
       )
      ->addColumn(
        $tableName,
        'qty_returned',
        array(
            'TYPE'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
            'SCALE'     => 4,
            'PRECISION' => 12,
            'DEFAULT'   => '0.0000',
            'NULLABLE'  => false,
            'COMMENT'   => 'Qty of returned items',
        )
    )
         ->addColumn(
        $tableName,
        'order_item_id',
        array(
            'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
            'integer'     => 11,
            'unsigned' =>true,
            'nullable' => false,
            'COMMENT'   => 'Order Item Id',
        )
    )
         ->addColumn(
        $tableName,
        'product_name',
        array(
            'TYPE'      => Varien_Db_Ddl_Table::TYPE_TEXT,
            'integer'     => 255,            
            'COMMENT'   => 'Product Name For Backend',
        )
    )
          ->addColumn(
        $tableName,
        'product_sku',
        array(
            'TYPE'      => Varien_Db_Ddl_Table::TYPE_TEXT,
            'integer'     => 255,            
            'COMMENT'   => 'Product Sku For Backend',
        )
    )
         ->addColumn(
        $tableName,
        'product_options',
        array(
            'TYPE'      => Varien_Db_Ddl_Table::TYPE_TEXT,
            'integer'     => 255,            
            'COMMENT'   => 'Product Options',
        )
    )
        ->addColumn(
        $tableName,
        'status',
        array(
            'TYPE'      => Varien_Db_Ddl_Table::TYPE_TEXT,
            'integer'     => 20,
            'nullable'=> false,            
            'COMMENT'   => 'Status',
        )
    );
$installer->endSetup();