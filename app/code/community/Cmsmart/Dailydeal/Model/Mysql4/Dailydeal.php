<?php
class Cmsmart_Dailydeal_Model_Mysql4_Dailydeal extends Mage_Core_Model_Mysql4_Abstract {

    public function _construct() {
        $this->_init('dailydeal/dailydeal', 'dealid');
    }

    public function addProductFromDeal($object, $arrExcludePdid = array()) {
		
		// return;
			if ($object->getData('linkrelatedproduct')) {
				$rlArray = (array)$object->getData();

				$rlArray['dealid'] = $object->getId();
				
				// print_r($rlArray); die();
				$rlArray['storeidlist'] = implode(',', $rlArray['stores']);
				
				unset($rlArray['price']);
				unset($rlArray['id']);
				unset($rlArray['links']);
				unset($rlArray['linkrelatedproduct']);
				unset($rlArray['stores']);
				unset($rlArray['form_key']);
				unset($rlArray['page']);
				unset($rlArray['limit']);
				unset($rlArray['entity_id']);
				unset($rlArray['related_productdailydeal']);
				unset($rlArray['name']);
				unset($rlArray['type']);
				unset($rlArray['sku']);
				unset($rlArray['position']);
				
				$format = Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
				$rlArray['closetime'] = Mage::getModel('core/date')->gmtDate(null, Mage::app()->getLocale()->date($rlArray['closetime'], $format)->getTimestamp());
				$rlArray['starttime'] = Mage::getModel('core/date')->gmtDate(null, Mage::app()->getLocale()->date($rlArray['starttime'], $format)->getTimestamp());
			
							
				foreach ((array) $object->getData('linkrelatedproduct') as $linkrelatedproduct) {
					if(!in_array($linkrelatedproduct, $arrExcludePdid)){
						$rlArray['title'] = (string)Mage::getModel('catalog/product')->load($linkrelatedproduct)->getData('name');
						$rlArray['productid'] = $linkrelatedproduct;
						// print_r($rlArray); break;
						
						if($rlArray['dealid']) $this->_getWriteAdapter()->insert($this->getTable('dailydealproducts'), $rlArray);
					}
					
				}
				
				// print_r($rlArray); die();
				//exit();
			}
			
	}
	
    protected function _afterSave(Mage_Core_Model_Abstract $object2) {
		$object = $object2;
		// $format = Varien_Date::DATETIME_INTERNAL_FORMAT;
		
		// if(@$object->getData('starttime')) $object->setData('starttime', Mage::getModel('core/date')->gmtDate(null, Mage::app()->getLocale()->date($object->getData('starttime'), $format)->getTimestamp()));
			
		// if(@$object->getData('closetime')) $object->setData('closetime', Mage::getModel('core/date')->gmtDate(null, Mage::app()->getLocale()->date($object->getData('closetime'), $format)->getTimestamp()));
		
		// echo '<br/>';		
		// echo $object->getData('starttime').'<br/>';		
		// echo $object->getData('closetime').'<br/>'; die();
		//print_r($object->getData());
		if(Mage::app()->getRequest()->getParam('id')){
		
			$dealid = (int)$object->getId();
			
			$cll = Mage::getModel('dailydeal/dailydealproducts')->getCollection()->addFieldToFilter('dealid', array('eq'=> $dealid));
			$ids = array();
			foreach($cll as $itemcll) $ids[] = $itemcll->getProductid();
			$pdids = $object->getData('linkrelatedproduct')?$object->getData('linkrelatedproduct'):$ids;
			
            $collection = Mage::getModel('dailydeal/dailydealproducts')->getCollection()
						->addFieldToFilter('dealid', array('neq'=> $dealid))
						// ->addFieldToFilter('id', array('nin' => $ids))
						->addFieldToFilter('productid', array('in' => $pdids))
						->addFieldToFilter(array('starttime','closetime'),
						array(
								array(
									'from' => $object->getData('starttime'),
									'to' => $object->getData('closetime'),
									'date' => true, // specifies conversion of comparison values
								),
								array(
									'from' => $object->getData('starttime'),
									'to' => $object->getData('closetime'),
									'date' => true, // specifies conversion of comparison values
								)
							)
						);
		

			if($collection->getSize() > 0){
				Mage::getSingleton('adminhtml/session')->addWarning(Mage::helper('dailydeal')->__('Error to saved the deal. The time of deal is not valid'));
				// $this->_redirect('*/*/adddeal', array('id' => $this->getRequest()->getParam('id')));
				return false;
			}
			// $condition = $this->_getWriteAdapter()->quoteInto('dealid = ?', $object->getId());
			// $this->_getWriteAdapter()->delete($this->getTable('store'), $condition);
			// $this->_getWriteAdapter()->delete($this->getTable('dailydealproducts'), $condition);	
			$adapter = $this->_getReadAdapter();
			if($object->getData('linkrelatedproduct')){
			
				
				$adapter->raw_query('UPDATE ' . $this->getTable('dailydealproducts') .' set `dealid` = 0 where `dealid` = '.$object->getId().' and productid not in ('.implode(',', $object->getData('linkrelatedproduct')).')');
				
				// $arr = $object->getData('linkrelatedproduct');
				// $arReject = array();
				// $ar = array();
				// foreach($arr as $a){
					// $ar[] = $a;
					// $arReject[] = $a;
				// }
				// if(count($aReject)){
					// Mage::getSingleton('adminhtml/session')->addWarning(Mage::helper('dailydeal')->__('The time of deal is not valid. Error to saved products have ids in'). ' '.implode(',', $aReject));
				// }
				
				$adapter->raw_query('UPDATE ' . $this->getTable('dailydealproducts') .' set `save` = "'.$object->getData('save').'", `status` = "'.$object->getData('status').'", `closetime` = "'.$object->getData('closetime').'", `starttime` = "'.$object->getData('starttime').'", `storeidlist` = "'.(implode(',', $object->getData('stores'))).'" where `dealid` = '.$object->getId().' and productid in ('.implode(',', $object->getData('linkrelatedproduct')).')');
				
			}else{
				$adapter->raw_query('UPDATE ' . $this->getTable('dailydealproducts') .' set `save` = "'.$object->getData('save').'", `status` = "'.$object->getData('status').'", `closetime` = "'.$object->getData('closetime').'", `starttime` = "'.$object->getData('starttime').'", `storeidlist` = "'.(implode(',', $object->getData('stores'))).'" where `dealid` = '.$object->getId().'');
			}
			
			
			$this->addProductFromDeal($object, $ids);
			
			return;
			
		}else{
			
			$this->addProductFromDeal($object);
			
        }
		
        return parent::_afterSave($object);
    }

    /**
     *
     * @param Mage_Core_Model_Abstract $object
     */
    protected function _afterLoad(Mage_Core_Model_Abstract $object) {
        $select = $this->_getReadAdapter()->select()
                ->from($this->getTable('store'))
                ->where('dealid = ?', $object->getId());

        if ($data = $this->_getReadAdapter()->fetchAll($select)) {
            $storesArray = array();
            foreach ($data as $row) {
                $storesArray[] = $row['store_id'];
            }
            $object->setData('store_id', $storesArray);
        }

        return parent::_afterLoad($object);
    }

    /**
     * Retrieve select object for load object data
     *
     * @param string $field
     * @param mixed $value
     * @return Zend_Db_Select
     */
    protected function _getLoadSelect($field, $value, $object) {

        $select = parent::_getLoadSelect($field, $value, $object);
        if ($object->getStoreId()) {
            $select->join(array('cps' => $this->getTable('store')), $this->getMainTable() . '.dealid = `cps`.dealid')
                    ->where('`cps`.store_id in (0, ?) ', $object->getStoreId())
                    ->order('store_id DESC')
                    ->limit(1);
        }
        return $select;
    }

    protected function _beforeDelete(Mage_Core_Model_Abstract $object) {

        // Cleanup stats on dailydeal delete
        $adapter = $this->_getReadAdapter();
        // 1. Delete dailydeal/store
        //$adapter->delete($this->getTable('dailydeal/store'), 'dealid=' . $object->getId());
        // 2. Delete dailydeal/post_cat
        //$adapter->delete($this->getTable('dailydeal/dailydeal'), 'dealid=' . $object->getId());
        // 3. Delete dailydeal/post_comment 
        //$adapter->delete($this->getTable('dailydeal/dailydeal'), 'dealid=' . $object->getId());
        // Update tags

        try {
            $tags = explode(",", $object->getTags());
            if (count($tags)) {
                foreach ($tags as $tag) {
                    Mage::getModel('dailydeal/tag')->loadByName($tag, null)->refreshCount();
                }
            }
        } catch (Exception $e) {
            
        }
    }

}
