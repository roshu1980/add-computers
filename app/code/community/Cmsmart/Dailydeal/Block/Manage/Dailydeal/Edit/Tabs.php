<?php


class Cmsmart_Dailydeal_Block_Manage_Dailydeal_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs {

    public function __construct() {
        parent::__construct();
        $this->setId('dailydeal_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('dailydeal')->__('Post Information'));
    }

    protected function _beforeToHtml() {
        $this->addTab('form_section', array(
            'label' => Mage::helper('dailydeal')->__('General Information'),
            'title' => Mage::helper('dailydeal')->__('General Information'),
            'content' => $this->getLayout()->createBlock('dailydeal/manage_dailydeal_edit_tab_form')->toHtml(),
        ));

        // $this->addTab('options_section', array(
            // 'label' => Mage::helper('dailydeal')->__('Select Products'),
            // 'title' => Mage::helper('dailydeal')->__('Select Products'),
            // 'content' => $this->getLayout()->createBlock('dailydeal/manage_dailydeal_edit_tab_options')->toHtml(),
        // ));

        $this->addTab('options_section', array(
            'label' => Mage::helper('dailydeal')->__('Select Products'),
			'url'       => $this->getUrl('*/*/products', array('_current' => true)),
			'class'     => 'ajax',
        ));
		

		
        return parent::_beforeToHtml();
    }

}
