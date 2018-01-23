<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$installer = $this;
 
$installer->startSetup();
 
$table = $installer->getConnection()
    ->newTable($installer->getTable('rma_order_item'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Entity Id')
    ->addColumn('rma_entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 11, array(        
         'unsigned' => true,
         'nullable'  => false,
          ), 'Rma Entity Id')
     ->addColumn('is_qty_decimal', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
            'visible'            => false,
            'sort_order'         => 15,
            'position'           => 15,
         ), 'Is item quantity decimal')
       ->addColumn('qty_requested', Varien_Db_Ddl_Table::TYPE_DECIMAL, array(
           'length' => '10,2',
           'nullable'  => false,
           'default' => 0.00,
         ), 'Qty of requested items')
       ->addColumn('qty_authorized', Varien_Db_Ddl_Table::TYPE_DECIMAL, array(     
           'length' => '10,2',
           'nullable'  => false,
           'default' => 0.00,
          ), 'Qty of authorized items')
       ->addColumn('qty_approved', Varien_Db_Ddl_Table::TYPE_DECIMAL, '10,2', array(
           'length' => '10,2',
           'nullable'  => false,
           'default' => 0.00,
          ), 'Qty of approved items') 
        ->addColumn('qty_returned', Varien_Db_Ddl_Table::TYPE_DECIMAL, array(
            'length' => '10,2',
           'nullable'  => false,
           'default' => 0.00,
          ), 'Qty of returned items')
        ->addColumn('order_item_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 11, array(
            'unsigned' =>true,
            'nullable' => false
           ), 'Order Item Id')
        ->addColumn('product_name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
                  ), 'Product Name For Backend')
         ->addColumn('product_sku', Varien_Db_Ddl_Table::TYPE_TEXT, 255,array(            
           ),'Product Sku For Backend')
        ->addColumn('product_options', Varien_Db_Ddl_Table::TYPE_TEXT, null,array(              
           ),'Product Options')
         ->addColumn('status', Varien_Db_Ddl_Table::TYPE_TEXT,20, array(
              'nullable'=> false,
          ),'Status')
         ->addForeignKey($installer->getFkName('rma_order_item', 'rma_entity_id', 'rma_order', 'entity_id'),
           'rma_entity_id', $installer->getTable('rma_order'), 'entity_id',
           Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE);                
$installer->getConnection()->createTable($table);
$installer->endSetup();