<?php
/*
* Name: Cmsm Navigation
* Author: The Cmsmart Development Team 
* Date Created:
* Websites: http://cmsmart.net
* Technical Support: Forum - http://cmsmart.net/support
* GNU General Public License v3 (http://opensource.org/licenses/GPL-3.0)
* Copyright © 2011-2013 Cmsmart.net. All Rights Reserved.
*/

class Cmsmart_Dailydeal_Block_Todaydeal extends Mage_Catalog_Block_Product_Abstract
{
	public $configP = '';
	
	protected function _prepareLayout()
	{
		parent::_prepareLayout();
		$AvailableLimit = array();
		$exp = explode(',', str_replace(', ', ',', (string)@$this->configP['availablelimit']));
		foreach($exp as $item) $AvailableLimit[$item] = $item;
		
		$pager = $this->getLayout()->createBlock('page/html_pager', 'custom.pager');
		$pager->setAvailableLimit($AvailableLimit);
		
		$pager->setCollection($this->getCollection());
		$this->setChild('pager', $pager);
		$this->getCollection()->load();
		return $this;
	}
	
	public function getPagerHtml()
	{
		return $this->getChildHtml('pager');
	}

	public function __construct()
	{
		
		
		$this->configP = Mage::getStoreConfig('dailydeal/general');
		parent::__construct();
		//$todayDate = date('Y-m-d H:i:s');
		$daysIsDailydeal = (int)$this->configP['daysdailydeal']?(int)$this->configP['daysdailydeal']:1;
		$todayDate = date('Y-m-d H:i:s');
		$tomorrow = mktime(0, 0, 0, date('m'), date('d') - $daysIsDailydeal, date('y'));
		$tomorrowDate = date('Y-m-d H:i:s', $tomorrow);
		
		// $catid = Mage::getSingleton('core/session')->getcatid();
		// $category = Mage::getModel('catalog/category')->load($catid);   
		$collection = Mage::getResourceModel('catalogsearch/advanced_collection')
			->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
			->addMinimalPrice()
			->addStoreFilter();

		Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);

		$collection->getSelect()->join(array('t2' => Mage::getSingleton('core/resource')->getTableName('dailydeal/dailydealproducts')),'(e.entity_id = t2.productid and t2.status != 2 and t2.starttime >= "'.$tomorrowDate.'" and t2.starttime <= "'.$todayDate.'" and t2.closetime >= "'.$todayDate.'")', array('t2.sold', 't2.save', 't2.quantity', 't2.starttime', 't2.closetime'));
			//$collection->addCategoryFilter($category)
		$collection->setPageSize($this->get_prod_count())
			 ->setCurPage($this->get_cur_page());

		$this->setCollection($collection);
	}
	
   protected function _afterToHtml($html)
    {
        return $html.@$this->helper('dailydeal')->getToAddStyle();
    }
}
?>