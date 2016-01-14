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
class Cmsmart_Dailydeal_Block_Imageicon extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	public function render(Varien_Object $row)
	{
		$value =  $row->getData($this->getColumn()->getIndex());
		//return '<span style="color:red;">'.$value.'</span>';
		//$arrImg = array('large', 'medium', 'small');
		if($value) return '<img style="width: 30px;" src="'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).''.$value.'" />';
		return '<img style="width: 30px;" src="'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'cmsmart/dailydeal/noimage.jpg" />';
	}
}
?>