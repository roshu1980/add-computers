﻿<?php
/*
* Name: Cmsm Navigation
* Author: The Cmsmart Development Team 
* Date Created:
* Websites: http://cmsmart.net
* Technical Support: Forum - http://cmsmart.net/support
* GNU General Public License v3 (http://opensource.org/licenses/GPL-3.0)
* Copyright © 2011-2013 Cmsmart.net. All Rights Reserved.
*/

class Cmsmart_Dailydeal_Block_Sidebar_Todaydeal extends Mage_Catalog_Block_Product_Abstract
{
	protected function _beforeToHtml()
	{
		parent::_beforeToHtml();
		$this->getCollection()->setPageSize($this->getPs())->setCurPage(1);
		return $this;
	}

	public function __construct()
	{
		parent::__construct();
		//$todayDate = date('Y-m-d H:i:s');
		$config = Mage::getStoreConfig('dailydeal/general');
		$daysIsDailydeal = (int)@$config['daysdailydeal']?(int)$config['daysdailydeal']:1;
		$todayDate = date('Y-m-d H:i:s');
		$tomorrow = mktime(0, 0, 0, date('m'), date('d') - $daysIsDailydeal, date('y'));
		$tomorrowDate = date('Y-m-d H:i:s', $tomorrow);
		$catid = Mage::getSingleton('core/session')->getcatid();
		$category = Mage::getModel('catalog/category')->load($catid);   
		$collection = Mage::getResourceModel('catalogsearch/advanced_collection')
			->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
			->addMinimalPrice()
			->addStoreFilter();

		Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);

		$collection->getSelect()->join(array('t2' => Mage::getSingleton('core/resource')->getTableName('dailydeal/dailydealproducts')),'(e.entity_id = t2.productid and t2.status != 2 and t2.starttime >= "'.$tomorrowDate.'" and t2.starttime <= "'.$todayDate.'" and t2.closetime >= "'.$todayDate.'")', array('t2.sold', 't2.save', 't2.starttime', 't2.closetime'));
		 $collection->setPageSize($this->getPs())->setCurPage(1);
		$this->setCollection($collection);
	}
	
	public function getPs(){
		return $this->getProductCount()?$this->getProductCount():5;
	}
   
   
}
?>