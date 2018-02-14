<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$installer = Mage::getResourceModel('customer/setup','customer_setup');
$installer->startSetup();

//$entity = $installer->getEntityTypeId('customer');
//
//if(!$installer->attributeExists("customer", 'bankname')) {
//    $installer->removeAttribute("customer", 'bankname');
//}


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