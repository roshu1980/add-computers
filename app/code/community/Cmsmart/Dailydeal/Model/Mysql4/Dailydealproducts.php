<?php


class Cmsmart_Dailydeal_Model_Mysql4_Dailydealproducts extends Mage_Core_Model_Mysql4_Abstract {

    public function _construct() {
        $this->_init('dailydeal/dailydealproducts', 'id');
    }

    public function load(Mage_Core_Model_Abstract $object, $value, $field=null) {
        // if (strcmp($value, (int) $value) !== 0) {
            // $field = 'post_id';
        // }
        return parent::load($object, $value, $field);
    }

}
