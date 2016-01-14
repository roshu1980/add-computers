<?php


class Cmsmart_Dailydeal_Model_Dailydeal extends Mage_Core_Model_Abstract {

	protected $_dailydealCollection=null;
	
    public function _construct() {
        parent::_construct();
        $this->_init('dailydeal/dailydeal');
    }

    public function getShortContent() {
        $content = $this->getData('short_content');
        if (Mage::getStoreConfig(Cmsmart_Dailydeal_Helper_Config::XML_BLOG_PARSE_CMS)) {
            $processor = Mage::getModel('core/email_template_filter');
            $content = $processor->filter($content);
        }
        return $content;
    }
	
    public function getDailydeals(){
		if (is_null($this->_dailydealCollection)) {
        $store = Mage::app()->getStore()->getStoreId();
        $this->_dailydealCollection = $this->getCollection()
                ->addFieldToFilter('status', 1);
                // ->addFieldToFilter('is_random',0)
                // ->addFieldToFilter('store_id',$this->getArrayFilter($store))
                // ->addFieldToFilter('product_id',array('nin'=>0));
				//->addFieldToFilter('close_time',array('nin'=>null));
		}
        return $this->_dailydealCollection;
    }
	
    public function getDealByProduct($productId) {
        $dailydeal = $this->getDailydeals()->addFieldToFilter('productid',$productId);
		if($dailydeal->getSize()) return $dailydeal->getFirstItem();
        return Mage::getModel('dailydeal/dailydeal');
    }

    public function getPostContent() {
        $content = $this->getData('post_content');
        if (Mage::getStoreConfig(Cmsmart_Dailydeal_Helper_Config::XML_BLOG_PARSE_CMS)) {
            $processor = Mage::getModel('core/email_template_filter');
            $content = $processor->filter($content);
        }
        return $content;
    }

    // public function _beforeSave() {
        // if (is_array($this->getData('tags'))) {
            // $this->setData('tags', implode(",", $this->getData('tags')));
        // }
        // return parent::_beforeSave();
    // }

}
