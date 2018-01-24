<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$installer = $this;
 
$installer->startSetup();
 
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
$installer->endSetup();