<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$installer = $this;
 
$installer->startSetup();
 
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
      ->addColumn('is_required', Varien_Db_Ddl_Table::TYPE_SMALLINT, 5,array(
          'unsigned' => true,
          'nullable'  => false,
         ), 'Is Required')
       ->addColumn('is_unique', Varien_Db_Ddl_Table::TYPE_SMALLINT, 5, array(
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