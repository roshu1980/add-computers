<?php


class Cmsmart_Dailydeal_Model_Mysql4_Dailydealproducts_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {

    public function _construct() {
        $this->_init('dailydeal/dailydealproducts');
    }

    // public function toOptionArray() {
        // return $this->_toOptionArray('dealid', 'dealid');
    // }

    public function addCatFilter($postId) {
        $this->getSelect()
                ->where('dealid = ?', $postId);
        return $this;
    }
	
	public function addStoreFilter($store) {
        // $this->getSelect()
                // ->where('dealid = ?', $postId);
		// print_r($store->getData('store_id'));
        $array = array();
		
		$arr = explode(',', (string)$store->getStoreId());
        if($store){
			foreach($arr as $a) {
				$array[] = array('finset' => $a);
			}
		}
		$array[] = array('finset' => 0);
		
		if($array) $this->addFieldToFilter('storeidlist', $array);
        return $this;
    }
}