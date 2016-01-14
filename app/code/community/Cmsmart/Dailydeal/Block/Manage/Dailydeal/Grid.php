<?php


class Cmsmart_Dailydeal_Block_Manage_Dailydeal_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('dailydealGrida');
        $this->setDefaultSort('created_time');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    protected function _getStore() {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }

    protected function _prepareCollection() {
        $collection = Mage::getModel('dailydeal/dailydeal')->getCollection();
        $store = $this->_getStore();
        if ($store->getId()) {
            $collection->addStoreFilter($store);
        }
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {
        $this->addColumn('dealid', array(
            'header' => Mage::helper('dailydeal')->__('ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'dealid',
        ));
		
        $this->addColumn('image', array(
            'header'    => Mage::helper('dailydeal')->__('Image'),
            'index'     => 'image',
			'filter' => false,
			'sortable' => false,
			'width' => '50px',
			'align' => 'center',
            'renderer'  => 'Cmsmart_Dailydeal_Block_Imageicon'
        ));
		
        $this->addColumn('title', array(
            'header' => Mage::helper('dailydeal')->__('Title'),
            'align' => 'left',
            'index' => 'title',
        ));

        $this->addColumn('save', array(
            'header' => Mage::helper('dailydeal')->__('Save'),
            'align' => 'left',
			'width' => '100px',
            'index' => 'save',
        ));

        $this->addColumn('quantity', array(
            'header' => Mage::helper('dailydeal')->__('Quantity'),
            'width' => '100px',
            'index' => 'quantity',
			
        ));


        $this->addColumn('created_time', array(
            'header' => Mage::helper('dailydeal')->__('Created at'),
            'index' => 'created_time',
            'type' => 'datetime',
            'width' => '120px',
            'gmtoffset' => true,
            'default' => ' -- '
        ));

        $this->addColumn('starttime', array(
            'header' => Mage::helper('dailydeal')->__('Start time'),
            'index' => 'starttime',
            'width' => '120px',
            'type' => 'datetime',
            'gmtoffset' => true,
            'default' => ''
        ));

        $this->addColumn('closetime', array(
            'header' => Mage::helper('dailydeal')->__('Close time'),
            'index' => 'closetime',
            'width' => '120px',
            'type' => 'datetime',
			'gmtoffset' => false,
            'default' => ''
        ));

		$statuses = Mage::getSingleton('dailydeal/status')->getOptionArray();

        $this->addColumn('status', array(
            'header' => Mage::helper('dailydeal')->__('Status'),
            'align' => 'left',
            'width' => '80px',
            'index' => 'status',
            'type' => 'options',
            'options' => $statuses,
        ));

        $this->addColumn('action', array(
            'header' => Mage::helper('dailydeal')->__('Action'),
            'width' => '100',
            'type' => 'action',
            'getter' => 'getId',
            'actions' => array(
                array(
                    'caption' => Mage::helper('dailydeal')->__('Edit'),
                    'url' => array('base' => '*/*/edit'),
                    'field' => 'id'
                )
            ),
            'filter' => false,
            'sortable' => false,
            'index' => 'stores',
            'is_system' => true,
        ));

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction() {
        $this->setMassactionIdField('post_id');
        $this->getMassactionBlock()->setFormFieldName('dailydeal');

        $this->getMassactionBlock()->addItem('delete', array(
            'label' => Mage::helper('dailydeal')->__('Delete'),
            'url' => $this->getUrl('*/*/massDelete'),
            'confirm' => Mage::helper('dailydeal')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('dailydeal/status')->getOptionArray();

        array_unshift($statuses, array('label' => '', 'value' => ''));
        $this->getMassactionBlock()->addItem('status', array(
            'label' => Mage::helper('dailydeal')->__('Change status'),
            'url' => $this->getUrl('*/*/massStatus', array('_current' => true)),
            'additional' => array(
                'visibility' => array(
                    'name' => 'status',
                    'type' => 'select',
                    'class' => 'required-entry',
                    'label' => Mage::helper('dailydeal')->__('Status'),
                    'values' => $statuses
                )
            )
        ));
        return $this;
    }

    public function getRowUrl($row) {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

}
