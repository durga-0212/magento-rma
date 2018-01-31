<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$installer = $this;
$installer->startSetup();
$this->run(" ALTER TABLE `{$this->getTable('rma_attributes')}` CHANGE `resolution` `resolution` ENUM('Refund','Store Credit')"); 
$this->run(" ALTER TABLE `{$this->getTable('rma_attributes')}` CHANGE `condition` `condition` ENUM('Packed','Unpacked') ");
$installer->endSetup();