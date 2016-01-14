<?php

class Cmsmart_Dailydeal_Model_Status extends Varien_Object {
    const STATUS_ACTIVE = 1;
    const STATUS_DISABLED = 2;
    const STATUS_EXPIRED = 3;
    const STATUS_COMING = 4;

			
    public function addEnabledFilterToCollection($collection) {
        $collection->addEnableFilter(array('in' => $this->getEnabledStatusIds()));
        return $this;
    }

    public function addCatFilterToCollection($collection, $cat) {
        $collection->addCatFilter($cat);
        return $this;
    }

    public function getEnabledStatusIds() {
        return array(self::STATUS_ACTIVE);
    }

    public function getDisabledStatusIds() {
        return array(self::STATUS_DISABLED);
    }

    public function getHiddenStatusIds() {
        return array(self::STATUS_HIDDEN);
    }

    static public function getOptionArray($all = false) {
		
        if($all) return array(
            self::STATUS_ACTIVE => Mage::helper('dailydeal')->__('Active'),
            self::STATUS_DISABLED => Mage::helper('dailydeal')->__('Disabled'),
            self::STATUS_EXPIRED => Mage::helper('dailydeal')->__('Expired'),
            self::STATUS_COMING => Mage::helper('dailydeal')->__('Coming'),
        );
		return array(
            self::STATUS_ACTIVE => Mage::helper('dailydeal')->__('Active'),
            self::STATUS_DISABLED => Mage::helper('dailydeal')->__('Disabled'),
            // self::STATUS_EXPIRED => Mage::helper('dailydeal')->__('Expired'),
            // self::STATUS_COMING => Mage::helper('dailydeal')->__('Coming'),
        );
    }
}
