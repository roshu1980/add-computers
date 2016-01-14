<?php
class Cmsmart_Dailydeal_Block_Catalog_Producttabs extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tabs
{
 
    protected function _prepareLayout()
    {
       
		 parent::_prepareLayout();
        $this->addTab('demo', array(
            'label'     => Mage::helper('catalog')->__('Demos'),
            'content'   => 'aaaaaaaa',
        ));       

    }
}