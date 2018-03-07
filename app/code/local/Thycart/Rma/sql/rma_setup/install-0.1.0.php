<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$installer = $this;
 
$installer->startSetup();

$installer->run("Drop table if exists `rma_order`;
CREATE TABLE `rma_order` (
`entity_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Entity Id',
`order_id` int(11) NOT NULL COMMENT 'Order Id',
`increment_id` int(11) NOT NULL COMMENT 'Increment Id',
`order_increment_id` int(11) NOT NULL COMMENT 'Order Increment ID',
`consignment_number` varchar(50) DEFAULT NULL,
`order_date` timestamp NULL DEFAULT NULL COMMENT 'Order Date',
`date_requested` timestamp NULL DEFAULT NULL COMMENT 'Date Requested',
`store_id` smallint(5) unsigned NOT NULL COMMENT 'Store Id',
`customer_id` int(10) unsigned NOT NULL COMMENT 'Customer Id',
`customer_name` varchar(255) NOT NULL COMMENT 'Customer Name',
`customer_email` varchar(255) NOT NULL COMMENT 'Customer Email',
`status` varchar(20) NOT NULL COMMENT 'Status',
PRIMARY KEY (`entity_id`),
KEY `FK_RMA_ORDER_CUSTOMER_ID_CUSTOMER_ENTITY_ENTITY_ID` (`customer_id`),
KEY `FK_RMA_ORDER_STORE_ID_CORE_STORE_STORE_ID` (`store_id`),
CONSTRAINT `FK_RMA_ORDER_CUSTOMER_ID_CUSTOMER_ENTITY_ENTITY_ID` FOREIGN KEY (`customer_id`) REFERENCES `customer_entity` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
CONSTRAINT `FK_RMA_ORDER_STORE_ID_CORE_STORE_STORE_ID` FOREIGN KEY (`store_id`) REFERENCES `core_store` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COMMENT='rma_order'");


$installer->run("Drop table if exists `rma_order_item`;
CREATE TABLE `rma_order_item` (
 `entity_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Entity Id',
 `rma_entity_id` int(11) unsigned NOT NULL COMMENT 'Rma Entity Id',
 `qty_ordered` decimal(12,4) NOT NULL DEFAULT '0.0000' COMMENT 'Qty of ordered items',
 `qty_requested` decimal(12,4) NOT NULL DEFAULT '0.0000' COMMENT 'Qty of requested items',
 `qty_approved` decimal(12,4) NOT NULL DEFAULT '0.0000' COMMENT 'Qty of approved items',
 `order_item_id` int(11) NOT NULL COMMENT 'Order Item Id',
 `product_id` int(11) NOT NULL,
 `product_name` text COMMENT 'Product Name For Backend',
 `product_price` decimal(12,4) DEFAULT NULL COMMENT 'PRODUCT PRICE',
 `product_sku` text COMMENT 'Product Sku For Backend',
 `item_status` text NOT NULL COMMENT 'Status',
 PRIMARY KEY (`entity_id`),
 KEY `rma_order_item_ibfk_1` (`rma_entity_id`),
 CONSTRAINT `rma_order_item_ibfk_1` FOREIGN KEY (`rma_entity_id`) REFERENCES `rma_order` (`entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='rma_order_item'
");


$installer->run("Drop table if exists `rma_order_history`;
CREATE TABLE `rma_order_history` (
 `entity_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Entity Id',
 `rma_entity_id` int(10) unsigned NOT NULL COMMENT 'Rma Entity Id',
 `is_customer_notified` int(10) DEFAULT NULL COMMENT 'Is Customer Notify',
 `is_visible_on_front` smallint(5) unsigned NOT NULL COMMENT 'Is Visible on front',
 `comment` varchar(255) NOT NULL COMMENT 'Comments',
 `status` varchar(20) NOT NULL COMMENT 'Status',
 `created_at` timestamp NULL DEFAULT NULL COMMENT 'Created date',
 `is_admin` smallint(5) unsigned NOT NULL COMMENT 'Is Admin',
 PRIMARY KEY (`entity_id`),
 KEY `FK_RMA_ORDER_HISTORY_RMA_ENTITY_ID_RMA_ORDER_ENTITY_ID` (`rma_entity_id`),
 CONSTRAINT `FK_RMA_ORDER_HISTORY_RMA_ENTITY_ID_RMA_ORDER_ENTITY_ID` FOREIGN KEY (`rma_entity_id`) REFERENCES `rma_order` (`entity_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='rma_order_history'
");


$installer->run("Drop table if exists `rma_attributes`;       
CREATE TABLE `rma_attributes` (
 `entity_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Entity Id',
 `rma_entity_id` int(10) unsigned NOT NULL COMMENT 'Rma Entity Id',
 `resolution` varchar(255) NOT NULL,
 `delivery_status` varchar(255) DEFAULT NULL,
 `reason` varchar(255) NOT NULL COMMENT 'Reason',
 `created_at` timestamp NULL DEFAULT NULL COMMENT 'Created date',
 PRIMARY KEY (`entity_id`),
 KEY `FK_RMA_ATTRIBUTES_RMA_ENTITY_ID_RMA_ORDER_ENTITY_ID` (`rma_entity_id`),
 CONSTRAINT `FK_RMA_ATTRIBUTES_RMA_ENTITY_ID_RMA_ORDER_ENTITY_ID` FOREIGN KEY (`rma_entity_id`) REFERENCES `rma_order` (`entity_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='rma_attributes'
");


$installer->run("Drop table if exists `rma_eav_attribute`; 	
CREATE TABLE `rma_eav_attribute` (
 `attribute_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Attribute Id',
 `attribute_code` varchar(255) NOT NULL COMMENT 'Attribute Code',
 `scope` varchar(50) NOT NULL COMMENT 'Scope',
 PRIMARY KEY (`attribute_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='rma_eav_attribute'
");    


$installer->run("Drop table if exists `rma_eav_attribute_option`; 
CREATE TABLE `rma_eav_attribute_option` (
`entity_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Entity Id',
`attribute_id` int(10) unsigned NOT NULL COMMENT 'Attribute Id',
`value` varchar(255) NOT NULL COMMENT 'Value',
PRIMARY KEY (`entity_id`),
KEY `FK_RMA_EAV_ATTR_OPT_ATTR_ID_RMA_EAV_ATTR_OPT_ATTR_ID` (`attribute_id`),
CONSTRAINT `FK_RMA_EAV_ATTR_OPT_ATTR_ID_RMA_EAV_ATTR_OPT_ATTR_ID` FOREIGN KEY (`attribute_id`) REFERENCES `rma_eav_attribute` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COMMENT='rma_eav_attribute_option'
");

$installer->run("INSERT INTO `rma_eav_attribute` (`attribute_id`, `attribute_code`,`scope`) VALUES ('1','resolution','3');
INSERT INTO `rma_eav_attribute` (`attribute_id`, `attribute_code`, `scope`) VALUES ('2','delivery_status','3');
INSERT INTO `rma_eav_attribute` (`attribute_id`, `attribute_code`,`scope`) VALUES ('3','item_status','3');
");


$installer->run("INSERT INTO `rma_eav_attribute_option` (`entity_id`, `attribute_id`, `value`) VALUES ('1','1','cancel');
INSERT INTO `rma_eav_attribute_option` (`entity_id`, `attribute_id`, `value`) VALUES ('2','1','refund');
INSERT INTO `rma_eav_attribute_option` (`entity_id`, `attribute_id`, `value`) VALUES ('3','2','not delivered');
INSERT INTO `rma_eav_attribute_option` (`entity_id`, `attribute_id`, `value`) VALUES ('4','2','delivered');
INSERT INTO `rma_eav_attribute_option` (`entity_id`, `attribute_id`, `value`) VALUES ('5','3','canceled');
INSERT INTO `rma_eav_attribute_option` (`entity_id`, `attribute_id`, `value`) VALUES ('6','3','complete');
INSERT INTO `rma_eav_attribute_option` (`entity_id`, `attribute_id`, `value`) VALUES ('7','3','payment request');
INSERT INTO `rma_eav_attribute_option` (`entity_id`, `attribute_id`, `value`) VALUES ('8','3','return received');
INSERT INTO `rma_eav_attribute_option` (`entity_id`, `attribute_id`, `value`) VALUES ('9','3','processing');
INSERT INTO `rma_eav_attribute_option` (`entity_id`, `attribute_id`, `value`) VALUES ('10','3','pending');
");

$installer->endSetup();

$installer = Mage::getResourceModel('customer/setup','customer_setup');

$installer->startSetup();

$installer->addAttribute("customer", "bankname",  array(
    "type"     => "varchar",
    "input"    => "text",
    'visible' => TRUE,
    'required' => FALSE,
    ))
   ->addAttribute("customer", "account_no",  array(
    "type"     => "varchar",
    "input"    => "text",
    'visible' => TRUE,
    'required' => FALSE,
    ))
    ->addAttribute("customer", "ifsc_code",  array(
    "type"     => "varchar",
    "input"    => "text",
    'visible' => TRUE,
    'required' => FALSE,
    ));

$installer->endSetup();