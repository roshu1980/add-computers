<?php


class Cmsmart_Dailydeal_Block_Manage_Alldeal extends Mage_Adminhtml_Block_Widget_Grid_Container {

    public function __construct() {
        $this->_controller = 'manage_alldeal';
        $this->_blockGroup = 'dailydeal';
        $this->_headerText = Mage::helper('dailydeal')->__('Dailydeal Post Manager');
        parent::__construct();
        $this->setTemplate('cmsmart/dailydeal/posts.phtml');
    }

    protected function _prepareLayout() {
        $this->setChild('add_new_button', $this->getLayout()->createBlock('adminhtml/widget_button')
                        ->setData(array(
                            'label' => Mage::helper('dailydeal')->__('Add Deal'),
                            'onclick' => "setLocation('" . $this->getUrl('*/*/adddeal') . "')",
                            'class' => 'add'
                        ))
        );
        /**
         * Display store switcher if system has more one store
         */
        if (!Mage::app()->isSingleStoreMode()) {
            $this->setChild('store_switcher', $this->getLayout()->createBlock('adminhtml/store_switcher')
                            ->setUseConfirm(false)
                            ->setSwitchUrl($this->getUrl('*/*/*', array('store' => null)))
            );
        }
        $this->setChild('grid', $this->getLayout()->createBlock('dailydeal/manage_alldeal_grid', 'alldeal.grid'));
        return parent::_prepareLayout();
    }

    public function getAddNewButtonHtml() {
        return $this->getChildHtml('add_new_button');
    }

    public function getGridHtml() {
        return $this->getChildHtml('grid');
    }

    public function getStoreSwitcherHtml() {
        return $this->getChildHtml('store_switcher');
    }

}
