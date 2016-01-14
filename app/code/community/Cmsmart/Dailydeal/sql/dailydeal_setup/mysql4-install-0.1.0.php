<?php
/*
* Name: Cmsmmart
* Author: The Cmsmart Development Team 
* Date Created:
* Websites: http://cmsmart.net
* Technical Support: Forum - http://cmsmart.net/support
* GNU General Public License v3 (http://opensource.org/licenses/GPL-3.0)
* Copyright © 2011-2013 Cmsmart.net. All Rights Reserved.
*/
 
$this->startSetup();
try{
$this->run("
ALTER TABLE {$this->getTable('sales/order')}
  DROP COLUMN `dailydeals`;    
");
}catch(Exception $e){}

$this->run("
ALTER TABLE {$this->getTable('sales/order')} ADD COLUMN `dailydeals` varchar(255) NULL;
CREATE TABLE IF NOT EXISTS `{$this->getTable('dailydeal/dailydeal')}` (
  `dealid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_desc` text,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `ishome` int(2) NOT NULL DEFAULT '0',
  `starttime` datetime NOT NULL,
  `closetime` datetime NOT NULL,
  `save` decimal(11,2) NOT NULL DEFAULT '0.00',
  `sold` int(11) NOT NULL DEFAULT '0',
  `quantity` int(11) NOT NULL DEFAULT '1000',
  `created_time` datetime NOT NULL,
  `update_time` datetime NOT NULL,
  `storeidlist` varchar(255) NOT NULL DEFAULT '0',
  PRIMARY KEY (`dealid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$this->getTable('dailydeal/dailydealproducts')}` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `productid` int(11) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_desc` text,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `ishome` int(2) NOT NULL DEFAULT '0',
  `starttime` datetime NOT NULL,
  `closetime` datetime NOT NULL,
  `save` decimal(11,2) NOT NULL DEFAULT '0.00',
  `sold` int(11) NOT NULL DEFAULT '0',
  `quantity` int(11) NOT NULL DEFAULT '1000',
  `created_time` datetime NOT NULL,
  `update_time` datetime NOT NULL,
  `dealid` int(11) NOT NULL DEFAULT '0',
  `storeidlist` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$this->getTable('dailydeal/relatedproductdailydeal')}` (
  `dealid` int(11) unsigned NOT NULL,
  `productid` int(11) NOT NULL,
  PRIMARY KEY (`dealid`,`productid`),
  KEY `attribute_id` (`dealid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$this->getTable('dailydeal/store')}` (
  `dealid` int(11) NOT NULL,
  `store_id` int(11) NOT NULL,
  PRIMARY KEY (`dealid`,`store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

$this->endSetup(); 