<?php

class Cmsmart_Dailydeal_Model_Mysql4_Dailydeal_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {

    protected $_previewFlag;

    protected function _construct() {
        $this->_init('dailydeal/dailydeal');
    }

    public function toOptionArray() {
        return $this->_toOptionArray('identifier', 'title');
    }

    public function addStoreFilter2($store) {

        if (!Mage::app()->isSingleStoreMode()) {

            if ($store instanceof Mage_Core_Model_Store) {
                $store = $store->getId();
            }

            $store = (array) $store;
            array_push($store, 0);

            $this->getSelect()
                    ->distinct()
                    ->join(array('store_table' => $this->getTable('store')), 'main_table.post_id = store_table.post_id', array())
                    ->where('store_table.store_id in (?)', array($store));
        }

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

    public function addStatusFilter($status = array(Cmsmart_Dailydeal_Model_Status::STATUS_ENABLED, Cmsmart_Dailydeal_Model_Status::STATUS_HIDDEN)) {

        if ($status == '*') {
            $status = array(Cmsmart_Dailydeal_Model_Status::STATUS_ENABLED, Cmsmart_Dailydeal_Model_Status::STATUS_HIDDEN, Cmsmart_Dailydeal_Model_Status::STATUS_DISABLED);
        }

        if (is_string($status)) {
            $status = (array) $status;
        }

        $this->getSelect()->where('main_table.status IN (?)', $status);

        return $this;
    }

}
