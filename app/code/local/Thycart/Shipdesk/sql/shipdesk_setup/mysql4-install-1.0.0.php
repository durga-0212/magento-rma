<?php
$installer = $this;
$installer->startSetup();

$installer->getConnection()
	->addColumn($installer->getTable('sales/shipment_track'),'track_url', array(
	    'type'     => Varien_Db_Ddl_Table::TYPE_TEXT,
	    'nullable' => true,
	    'comment'  => 'Track URL' 
    ));
$installer->endSetup();