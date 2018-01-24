<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$installer = $this;
 
$installer->startSetup();
 
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
$installer->endSetup();