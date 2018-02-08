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
         'unsigned' => true,
         'nullable'  => false,
          ), 'Order Id')
      ->addColumn('increment_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 11,array(
          'unsigned' => true,
          'nullable'  => false,
         ), 'Increment Id')
       ->addColumn('order_increment_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 11, array(
           'unsigned' => true,
           'nullable'  => false,
         ), 'Order Increment ID')
       ->addColumn('order_date', Varien_Db_Ddl_Table::TYPE_DATE, null, array(
          ), 'Order Date')
        ->addColumn('date_requested', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
          ), 'Date Requested') 
        ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 11,array(
            'unsigned' => true,
            'nullable' => false
          ), 'Store Id')
        ->addColumn('customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 11, array(
            'unsigned' =>true,
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
    ->addColumn('qty_ordered', Varien_Db_Ddl_Table::TYPE_DECIMAL, 255, array(
            'length' => '4,12',
            'nullable'  => false,
            'default' => 0.00,
         ), 'Qty of Ordered Items')    
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

$table = $installer->getConnection()
    ->newTable($installer->getTable('rma_order_history'))
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
      ->addColumn('is_customer_notified', Varien_Db_Ddl_Table::TYPE_INTEGER, 11,array(
          'nullable'  => true,
         ), 'Is Customer Notify')
       ->addColumn('is_visible_on_front', Varien_Db_Ddl_Table::TYPE_SMALLINT, 5, array(
           'unsigned' => true,
           'nullable'  => false,
         ), 'Is Visible on front')
        ->addColumn('comment', Varien_Db_Ddl_Table::TYPE_TEXT,255, array(
              'nullable'=> false,
          ),'Comments')
        ->addColumn('status', Varien_Db_Ddl_Table::TYPE_TEXT,20, array(
              'nullable'=> false,
          ),'Status')       
        ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
          ), 'Created date') 
        ->addColumn('is_admin', Varien_Db_Ddl_Table::TYPE_SMALLINT, 5, array(
           'unsigned' => true,
           'nullable'  => false,
         ), 'Is Admin')       
         ->addForeignKey($installer->getFkName('rma_order_history', 'rma_entity_id', 'rma_order', 'entity_id'),
           'rma_entity_id', $installer->getTable('rma_order'), 'entity_id',
           Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE);                

$installer->getConnection()->createTable($table);

$table = $installer->getConnection()
    ->newTable($installer->getTable('rma_attributes'))
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
    ->addColumn('resolution', Varien_Db_Ddl_Table::TYPE_SMALLINT, 5,array(
          'nullable'  => true,
          ), 'Resolution')
    ->addColumn('condition', Varien_Db_Ddl_Table::TYPE_SMALLINT, 5, array(
           'unsigned' => true,
           'nullable'  => false,
          ), 'Condition')
    ->addColumn('reason', Varien_Db_Ddl_Table::TYPE_TEXT,255, array(
              'nullable'=> false,
          ),'Reason')
    ->addColumn('filename', Varien_Db_Ddl_Table::TYPE_TEXT,255, array(
              'nullable'=> false,
          ),'Image Filename')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
          ), 'Created date') 
    ->addColumn('status', Varien_Db_Ddl_Table::TYPE_TEXT,20, array(
              'nullable'=> false,
          ),'Status')       
    ->addForeignKey($installer->getFkName('rma_attributes', 'rma_entity_id', 'rma_order', 'entity_id'),
           'rma_entity_id', $installer->getTable('rma_order'), 'entity_id',
           Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE);                

$installer->getConnection()->createTable($table);


$table = $installer->getConnection()
    ->newTable($installer->getTable('rma_eav_attribute'))
    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Attribute Id')
    ->addColumn('attribute_code', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(        
        'unsigned' => true,
        'nullable'  => false,
        ), 'Attribute Code')
    ->addColumn('scope', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(        
        'unsigned' => true,
        'nullable'  => false,
        ), 'Attribute Code')    
    ->addColumn('is_required', Varien_Db_Ddl_Table::TYPE_VARCHAR,255,array(
        'unsigned' => true,
        'nullable'  => false,
        ), 'Is Required')
    ->addColumn('is_unique', Varien_Db_Ddl_Table::TYPE_VARCHAR,255, array(
        'unsigned' => true,
        'nullable'  => false,
        ), 'Is Unique');
$installer->getConnection()->createTable($table);

$table = $installer->getConnection()
    ->newTable($installer->getTable('rma_eav_attribute_option'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Entity Id')
     ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 11, array(        
         'unsigned' => true,
         'nullable'  => false,
          ), 'Attribute Id')
      ->addColumn('value', Varien_Db_Ddl_Table::TYPE_VARCHAR,255,array(
          'unsigned' => true,
          'nullable'  => false,
         ), 'Value')
       ->addForeignKey($installer->getFkName('rma_eav_attribute_option', 'attribute_id', 'rma_eav_attribute_option', 'attribute_id'),
           'attribute_id', $installer->getTable('rma_eav_attribute'), 'attribute_id',
           Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE);

$installer->getConnection()->createTable($table);

$installer->endSetup();