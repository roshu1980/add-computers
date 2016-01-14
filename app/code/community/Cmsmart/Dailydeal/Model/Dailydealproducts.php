<?php

class Cmsmart_Dailydeal_Model_Dailydealproducts extends Mage_Core_Model_Abstract {
    const NOROUTE_PAGE_ID = 'no-route';

    protected function _construct() {
        $this->_init('dailydeal/dailydealproducts');
    }

    public function load($id, $field=null) {
        return parent::load($id, $field);
    }
	
    public function getDailydeals(){
		if (is_null($this->_dailydealCollection)) {
        $store = Mage::app()->getStore()->getStoreId();
        $this->_dailydealCollection = $this->getCollection()
                ->addFieldToFilter('status', array(1,3,4));
                // ->addFieldToFilter('is_random',0)
                // ->addFieldToFilter('store_id',$this->getArrayFilter($store))
                // ->addFieldToFilter('product_id',array('nin'=>0));
				//->addFieldToFilter('close_time',array('nin'=>null));
		}
        return $this->_dailydealCollection;
    }
	
    public function getDealByProduct($productId) {
        $dailydeal = $this->getDailydeals()->addFieldToFilter('productid', $productId);
		if($checkdate){
			$todayDate = date('Y-m-d H:i:s');
			$dailydeal->addFieldToFilter('closetime',array('gt' => $todayDate));
			$dailydeal->addFieldToFilter('starttime',array('lt' => $todayDate));
			// $dailydeal->addAttributeToFilter('closetime', array(
				// 'from' => '10 September 2000',
				// 'to' => '11 September 2000',
				// 'date' => true,
			// ));
			// echo $dailydeal->getSelect();
		}
		
		if($dailydeal->getSize()) return $dailydeal->getFirstItem();
        return Mage::getModel('dailydeal/dailydealproducts');
    }

    public function getActiveDealByProduct($productId) {
        $dailydeal = $this->getDailydeals()->addFieldToFilter('productid', $productId);

		$todayDate = date('Y-m-d H:i:s');
		$dailydeal->addFieldToFilter('closetime',array('gt' => $todayDate));
		$dailydeal->addFieldToFilter('starttime',array('lt' => $todayDate));
		// $dailydeal->addAttributeToFilter('closetime', array(
			// 'from' => '10 September 2000',
			// 'to' => '11 September 2000',
			// 'date' => true,
		// ));
		// echo $dailydeal->getSelect();

		
		if($dailydeal->getSize()) return $dailydeal->getFirstItem();
        return Mage::getModel('dailydeal/dailydealproducts');
    }
	
    public function noRoutePage() {
        $this->setData($this->load(self::NOROUTE_PAGE_ID, $this->getIdFieldName()));
        return $this;
    }

}
