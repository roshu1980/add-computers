<?php
class Cmsmart_Dailydeal_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function getToAddStyle(){
		if(Mage::registry('hasdealstyle')) return '';
		Mage::register('hasdealstyle', true);
		$config = Mage::getStoreConfig('dailydeal/general', $store = false);
		return '
			<style style="text/css">
				.featuredcenter .dailydeal_bg,.dailydeal_center_block .dailydeal_bg, .dailydeal_product_view .dailydeal_bg{
				background: none repeat scroll 0 0 '.(@$config['colortimeleft1']?$config['colortimeleft1']:'#DCE9F2').';
			}
			.dailydeal_bg > span {
				background: none repeat scroll 0 0 '.(@$config['colortimeleft2']?$config['colortimeleft2']:'#FEFEFE').';
				box-shadow: 0 1px 2px '.(@$config['boxshadowcolortimeleft']?$config['boxshadowcolortimeleft']:'#555555').';
			}
			</style>
		';
	}
	public function updateDealStatus()
    {
        $dailydeals  =  Mage::getModel('dailydeal/dailydealproducts')->getCollection()
                    //->addFieldToFilter('status', array('nin' => array(2,3)));
                    ->addFieldToFilter('status', array('nin' => array(2)));
        $store = Mage::app()->getStore()->getStoreId();
        if ($store != 0)        
            $dailydeals->addFieldToFilter('storeidlist',array(
				array('finset' => $store),
				array('finset' => 0),
			));                
        // echo 123456;     
        try {
            $time_now = Mage::getModel('core/date')->timestamp(time());
            foreach($dailydeals as $dailydeal)
            {
				
				try {
					$start_time = Mage::getModel('core/date')->timestamp(strtotime($dailydeal->getStarttime()));
                    $close_time = Mage::getModel('core/date')->timestamp(strtotime($dailydeal->getClosetime()));
				} catch (Exception $e) {
					continue;
				}
				
				if ($time_now <= $close_time && $time_now >= $start_time) $dailydeal->setStatus(1);
				if ($time_now > $close_time ) $dailydeal->setStatus(3);
				if ($start_time > $time_now ) $dailydeal->setStatus(4);					
				$dailydeal->save();
            }
        } catch(Exception $e) {}
    }
	
	public function cuttext($str, $limit = 0)
    {
		if(!$limit) return $str;
		return trim(substr(trim(strip_tags($str)), 0, $limit));
	}
}