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

class Cmsmart_Dailydeal_Block_Dailydeal extends Mage_Catalog_Block_Product_Abstract
{
	public $configP = '';
	protected function _prepareLayout()
	{
		parent::_prepareLayout();
		try{
			$AvailableLimit = array();
			$exp = explode(',', str_replace(', ', ',', (string)@$this->configP['availablelimit']));
			foreach($exp as $item) $AvailableLimit[$item] = $item;
			
			$pager = $this->getLayout()->createBlock('page/html_pager', 'custom.pager');
			$pager->setAvailableLimit($AvailableLimit);
			
			$pager->setCollection($this->getCollection());
			$this->setChild('pager', $pager);
			//$this->getCollection()->load();
		} catch(Exception $ex){
			
		}
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
		
		$maxday = 350;
		$from = (int)@$_GET['from'];
		$to = (int)@$_GET['to'];
		$to = $to == $maxday?0:$to;
		$datefrom = date('Y-m-d H:i:s', mktime(0, 0, 0, date('m'), date('d')+ $from, date('y')));
		$dateto = date('Y-m-d H:i:s', mktime(0, 0, 0, date('m'), date('d') + $to, date('y')));

		$todayDate = date('Y-m-d H:i:s');
		//echo $todayDate;
		// $todayDate = '2014-01-11 11:24:39 ';
		// $dateFrom = Mage::app()->getLocale()->date($todayDate, Varien_Date::DATETIME_INTERNAL_FORMAT);
		// echo Mage::getModel('core/date')->gmtDate(null, time());
		//echo Mage::getModel('core/date')->gmtDate();
		//echo Mage::getModel('core/date')->gmtDate(null, time());
		// echo $dateFrom = Mage::app()->getLocale()->date(date('Y-m-d H:i:s'), Varien_Date::DATETIME_INTERNAL_FORMAT);
		
		$tomorrow = mktime(0, 0, 0, date('m'), date('d')+1, date('y'));
		$tomorrowDate = date('m/d/y', $tomorrow);
		$collection = Mage::getResourceModel('catalogsearch/advanced_collection')
		->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
		->addMinimalPrice()
		->addStoreFilter();
		

		Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
		$collection->getSelect()->join(array('t2' => Mage::getSingleton('core/resource')->getTableName('dailydeal/dailydealproducts')),'(e.entity_id = t2.productid and t2.status != 2 '.($from?' and t2.closetime >= "'.$datefrom.'"':' and t2.starttime <= "'.$todayDate.'"').($to?' and t2.closetime <= "'.$dateto.'"':'').' and t2.closetime >= "'.$todayDate.'")', array('t2.sold', 't2.save', 't2.quantity', 't2.starttime', 't2.closetime'));
	
		// Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
		// $collection->getSelect()->join(array('t2' => Mage::getSingleton('core/resource')->getTableName('dailydeal/dailydealproducts')),'e.entity_id = t2.productid ', array('t2.sold', 't2.save', 't2.quantity', 't2.starttime', 't2.closetime'));
		
		//$collection->getSelect()->group('e.entity_id');
		// echo count($collection);
		//$collection->setPageSize($this->get_prod_count())->setCurPage($this->get_cur_page());
		$this->setCollection($collection);
	}
	
   protected function _afterToHtml($html)
    {
        return $html.@$this->helper('dailydeal')->getToAddStyle();
    }
}
?>