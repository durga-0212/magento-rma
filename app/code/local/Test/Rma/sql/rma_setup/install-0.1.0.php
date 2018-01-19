<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$installer = $this;
 
$installer->startSetup();
 
$table = $installer->getConnection()
    ->newTable($installer->getTable('rma_order'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Entity Id')
     ->addColumn('order_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 11, array(        
         'unasigned' => true,
         'nullable'  => false,
          ), 'Order Id')
      ->addColumn('increment_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 11,array(
          'unasigned' => true,
          'nullable'  => false,
         ), 'Increment Id')
       ->addColumn('order_increment_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 11, array(
           'unasigned' => true,
           'nullable'  => false,
         ), 'Order Increment ID')
       ->addColumn('order_date', Varien_Db_Ddl_Table::TYPE_DATE, null, array(
          ), 'Order Date')
        ->addColumn('date_requested', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
          ), 'Date Requested') 
        ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 11,array(
            'unasigned' => true,
            'nullable' => false
          ), 'Store Id')
        ->addColumn('customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 11, array(
            'unasigned' =>true,
            'nullable' => false
           ), 'Customer Id')
        ->addColumn('customer_name', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
            'nullable' => false,
            ), 'Customer Name')
         ->addColumn('customer_email', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255,array(
            'nullable' => false,    
           ),'Customer Email')
         ->addColumn('status', Varien_Db_Ddl_Table::TYPE_TEXT,20, array(
              'nullable'=> false,
          ),'Status')
         ->addForeignKey($installer->getFkName('rma_order', 'customer_id', 'customer_entity', 'entity_id'),
           'customer_id', $installer->getTable('customer_entity'), 'entity_id',
           Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
         ->addForeignKey($installer->getFkName('rma_order', 'store_id', 'core_store', 'store_id'),
           'store_id', $installer->getTable('core_store'), 'store_id',
            Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE);        
$installer->getConnection()->createTable($table);
$installer->endSetup();