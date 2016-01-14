<?php
class Cmsmart_Dailydeal_Block_Productview extends Mage_Core_Block_Template
{
    public function getProduct() {
		return Mage::registry('current_product');
	}
	
    public function getdealByProduct($productId){	
		// Mage::helper('dailydeal')->updateDealStatus();
        $deal = Mage::getModel('dailydeal/dailydealproducts')->getActiveDealByProduct($productId);
        return $deal;
    }
	
	protected function _afterToHtml($html)
    {
        return $html.@$this->helper('dailydeal')->getToAddStyle();
    }
}