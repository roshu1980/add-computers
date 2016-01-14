<?php


class Cmsmart_Dailydeal_Block_Manage_Alldeal_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs {

    public function __construct() {
        parent::__construct();
        $this->setId('dailydeal_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('dailydeal')->__('Post Information'));
    }

    protected function _beforeToHtml() {
	
		$id = $this->getRequest()->getParam('id');
		if(!$id){
			$this->addTab('options_section', array(
				'label' => Mage::helper('dailydeal')->__('Select Products'),
				'url'       => $this->getUrl('*/*/allproducts', array('_current' => true)),
				'class'     => 'ajax',
			));
		}
		
        $this->addTab('form_section', array(
            'label' => Mage::helper('dailydeal')->__('General Information'),
            'title' => Mage::helper('dailydeal')->__('General Information'),
            'content' => $this->getLayout()->createBlock('dailydeal/manage_alldeal_edit_tab_form')->toHtml(),
        ));

		if($id){
			$this->addTab('form_dealorder', array(
			  'label'     => Mage::helper('dailydeal')->__('Sold Items'),
			  'title'     => Mage::helper('dailydeal')->__('Sold Items'),
			  // 'class'     => 'ajax',
			  // 'url'       => $this->getUrl('*/*/sales', array('_current' => true)),
			  'content'   => $this->getLayout()->createBlock('dailydeal/manage_alldeal_edit_tab_listorder')->toHtml(),
			));
		}
		
        // $this->addTab('options_section', array(
            // 'label' => Mage::helper('dailydeal')->__('Select Products'),
            // 'title' => Mage::helper('dailydeal')->__('Select Products'),
            // 'content' => $this->getLayout()->createBlock('dailydeal/manage_dailydeal_edit_tab_options')->toHtml(),
        // ));


		

		
        return parent::_beforeToHtml();
    }
	
	public function getDailydeal()     
    { 
        if (!$this->hasData('dailydeal_data')) {
                $this->setData('dailydeal_data', Mage::registry('dailydeal_data'));
        }
        return $this->getData('dailydeal_data');   
    }

}
