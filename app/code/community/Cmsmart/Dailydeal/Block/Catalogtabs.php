<?php
class Cmsmart_Dailydeal_Block_Catalogtabs extends Mage_Adminhtml_Block_Catalog_Category_Tabs
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