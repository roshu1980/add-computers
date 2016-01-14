<?php


class Cmsmart_Dailydeal_Block_Manage_Dailydeal_Edit_Tab_Products extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('related_product_grid');
        $this->setDefaultSort('entity_id');
        $this->setUseAjax(true);
		if($this->getRequest()->getParam('id')) $this->setDefaultFilter(array('related_productdailydeal'=>1));

    }

    protected function _addColumnFilterToCollection($column) {
        // Set custom filter for in category flag
        if ($column->getId() == 'related_productdailydeal') {
            $productIds = $this->_getSelectedProducts();
            if (empty($productIds)) {
                $productIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', array('in' => $productIds));
            } elseif (!empty($productIds)) {
                $this->getCollection()->addFieldToFilter('entity_id', array('nin' => $productIds));
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    protected function _prepareCollection() {
		
        $collection = Mage::getModel('catalog/product')->getCollection()
                ->addAttributeToSelect('name')
                ->addAttributeToSelect('sku')
                ->addAttributeToSelect('price')
                ->addStoreFilter($this->getRequest()->getParam('store'))
                ->joinField('position', 'catalog/category_product', 'position', 'product_id=entity_id', 'category_id=' . (int) $this->getRequest()->getParam('id', 0), 'left');
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {
        $this->addColumn('related_productdailydeal', array(
            'header_css_class' => 'a-center',
            'type' => 'checkbox',
            //'name' => 'related_productdailydeal',
			'field_name' => 'related_productdailydeal[]',
            'values' => $this->_getSelectedProducts(),
            'align' => 'center',
            'index' => 'entity_id'
        ));
        $this->addColumn('entity_id', array(
            'header' => Mage::helper('catalog')->__('ID'),
            'sortable' => true,
            'width' => '60px',
            'index' => 'entity_id'
        ));
        $this->addColumn('name', array(
            'header' => Mage::helper('catalog')->__('Name'),
            'index' => 'name'
        ));
		
		$this->addColumn('type', array(
            'header'    => Mage::helper('catalog')->__('Type'),
            'width'     => 100,
            'index'     => 'type_id',
            'type'      => 'options',
            'options'   => Mage::getSingleton('catalog/product_type')->getOptionArray(),
        ));
		
        $this->addColumn('sku', array(
            'header' => Mage::helper('catalog')->__('SKU'),
            'width' => '120px',
            'index' => 'sku'
        ));
        $this->addColumn('price', array(
            'header' => Mage::helper('catalog')->__('Price'),
            'type' => 'currency',
            'currency_code' => (string) Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE),
            'index' => 'price'
        ));
        $this->addColumn('position', array(
            'header' => Mage::helper('catalog')->__('Position'),
            'name' => 'position',
            'type' => 'number',
            'validate_class' => 'validate-number',
            'index' => 'position',
            'width' => '60px',
            'editable' => true,
        ));

        return parent::_prepareColumns();
    }

    public function getGridUrl() {
        return $this->getUrl('*/*/products', array('_current' => true));
    }

	
	protected function _getSelectedProducts()
    {
        $products = $this->getRelatedProductdailydeal();
		
        if (!$products) {
            $products = array_keys($this->getSelectedProducts());
        }
		
        return $products;
    }

    /**
     * Retrieve related products
     *
     * @return array
     */
    public function getSelectedProducts()
    {
		$products = array();
        //return array(161 => array('position' => 161), 162 => array('position' => 162), 163 => array('position' => 163));	
        // return array();
		$id = $this->getRequest()->getParam('id');
		if($id){
			//$collection = Mage::getModel('dailydeal/related')->getCollection()->addCatFilter($id);
			$collection = Mage::getModel('dailydeal/dailydealproducts')->getCollection()->addCatFilter($id);
			// $collection = Mage::getModel('catalog/product')->getCollection()
                // ->addAttributeToSelect('name')
                // ->addAttributeToSelect('sku')
                // ->addAttributeToSelect('price')
                // ->addStoreFilter($this->getRequest()->getParam('store'));
                // ->joinField('position', 'catalog/category_product', 'position', 'product_id=entity_id', 'category_id=' . (int) $this->getRequest()->getParam('id', 0), 'left');
				
			foreach ($collection as $product) {
				$products[$product->getProductid()] = array('position' => 0);
			}
		}
        return $products;
    }

}
