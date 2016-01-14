<?php


class Cmsmart_Dailydeal_Manage_DailydealController extends Mage_Adminhtml_Controller_Action {

    public function preDispatch() {
        parent::preDispatch();
    }

    protected function _isAllowed() {
        return Mage::getSingleton('admin/session')->isAllowed('admin/dailydeal/dailydeal');
    }

    protected function _initAction() {
        $this->loadLayout()
                ->_setActiveMenu('dailydeal/dailydeal');

        return $this;
    }

    public function indexAction() {

        $this->displayTitle('Deals');
        $this->_initAction()
                ->renderLayout();
    }

    public function alldealAction() {
		Mage::helper('dailydeal')->updateDealStatus();
        $this->displayTitle('Deals');
        $this->_initAction()
                ->renderLayout();
    }

    public function editAction() {

        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('dailydeal/dailydeal')->load($id);

        if ($model->getId() || $id == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }

            Mage::register('dailydeal_data', $model);

            $this->loadLayout();
            $this->_setActiveMenu('dailydeal/dailydeal');
            $this->displayTitle('Edit Deal Group');

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

            $this->_addContent($this->getLayout()->createBlock('dailydeal/manage_dailydeal_edit'))
                    ->_addLeft($this->getLayout()->createBlock('dailydeal/manage_dailydeal_edit_tabs'));

            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('dailydeal')->__('Deal does not exist'));
            $this->_redirect('*/*/');
        }
    }

    public function newAction() {
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('dailydeal/dailydeal')->load($id);

        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }
		$model->setStores(explode(',', @$model->getStoreidlist()));
		
        Mage::register('dailydeal_data', $model);

        $this->loadLayout();
        $this->_setActiveMenu('dailydeal/newdailydeal');
        $this->displayTitle('Add new Deal');

        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

        $this->_addContent($this->getLayout()->createBlock('dailydeal/manage_dailydeal_edit'));;
        $this->_addLeft($this->getLayout()->createBlock('dailydeal/manage_dailydeal_edit_tabs'));

        $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);

        $this->renderLayout();
    }

    public function adddealAction() {
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('dailydeal/dailydealproducts')->load($id);

        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }
		$model->setStores(explode(',', @$model->getStoreidlist()));
        Mage::register('dailydeal_data', $model);

        $this->loadLayout();
        $this->_setActiveMenu('dailydeal/adddeal');
        $this->displayTitle('Add new Deal');

        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

        $this->_addContent($this->getLayout()->createBlock('dailydeal/manage_alldeal_edit'));;
        $this->_addLeft($this->getLayout()->createBlock('dailydeal/manage_alldeal_edit_tabs'));

        $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);

        $this->renderLayout();
    }
	
	public function productsAction()
	{
		$this->loadLayout();
		//if(@$_GET['ajax'] == true) $this->getLayout()->unsetBlock('related_grid_serializer');
		if(@$_GET['ajax'] == true) $this->getLayout()->getBlock('related_grid_serializer')->setFormId(true);
		$this->getLayout()->getBlock('catalog.product.edit.tab.selected')
            ->setRelatedProductdailydeal($this->getRequest()->getPost('related_productdailydeal', null));
		$this->renderLayout();
	}
	
	public function allproductsAction()
	{
		$this->loadLayout();
		if(@$_GET['ajax'] == true) $this->getLayout()->getBlock('related_grid_serializer')->setFormId(true);
		$this->renderLayout();
	}
	
	public function salesAction()
	{
		$this->loadLayout();
		$this->renderLayout();
	}

    public function duplicateAction() {
        $oldIdentifier = $this->getRequest()->getParam('identifier');
        $i = 1;
        $newIdentifier = $oldIdentifier . $i;
        while (Mage::getModel('dailydeal/deal')->loadByIdentifier($newIdentifier)->getData())
            $newIdentifier = $oldIdentifier . ++$i;
        $this->getRequest()->setDeal('identifier', $newIdentifier);
        $this->_forward('save');
    }

    public function saveAction() {

        if ($data = $this->getRequest()->getPost()) {
			$links = $this->getRequest()->getPost('links');
			// print_r($data); die();
			
			$data['linkrelatedproduct'] = array();
			if (isset($links['productdeal'])) {
				$data['linkrelatedproduct'] = array_keys(Mage::helper('adminhtml/js')->decodeGridSerializedInput($links['productdeal']));
			}
			if(@$data['quantity'] < 1) $data['quantity'] = 1;
			if(@$data['save'] > 100) $data['save'] = 100;
			if(@$data['save'] <= 0) $data['save'] = 1;

	
            $model = Mage::getModel('dailydeal/dailydeal');

            if (isset($data['stores']) || false) { //get all children
                if ($data['stores'][0] == 0) {
                    unset($data['stores']);
                    $data['stores'] = array();
                    $stores = Mage::getSingleton('adminhtml/system_store')->getStoreCollection();
                    foreach ($stores as $store)
                        $data['stores'][] = $store->getId();
                }
            }
			
			$data['storeidlist'] = implode(',', $data['stores']);
			$this->_filterDateTime($data, array('closetime','starttime'));

            try {

                $format = Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM);
				
				$closetime = Mage::app()->getLocale()->date($data['closetime'], $format)->getTimestamp();
				$starttime = Mage::app()->getLocale()->date($data['starttime'], $format)->getTimestamp();
				
				if($starttime > $closetime){
				
					Mage::getSingleton('adminhtml/session')->addError($this->__('The starttime is greater than the close time'));
					Mage::getSingleton('adminhtml/session')->setFormData($data);
					$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
					return;
				}
				
				// echo Mage::app()->getLocale()->date($data['closetime'], $format); die();
                $dateFrom = Mage::app()->getLocale()->date($data['closetime'], $format);
				//$dateFrom = Mage::app()->getLocale()->date($data['created_time'], $format);
				if(@$data['starttime']) $data['starttime'] =  Mage::getModel('core/date')->gmtDate(null, $starttime);
				
				if(@$data['closetime']) $data['closetime'] =  Mage::getModel('core/date')->gmtDate(null, $closetime);
				
				if (isset($_FILES['image']['name']) and (file_exists($_FILES['image']['tmp_name']))) {
					$uploader = new Varien_File_Uploader('image');
					$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
					$uploader->setAllowRenameFiles(false);
					$uploader->setFilesDispersion(false);
					$path = Mage::getBaseDir('media') . DS . 'cmsmart/dailydeal/' ;
					$uploader->save($path, $_FILES['image']['name']);
					$data['image'] = 'cmsmart/dailydeal/'.$_FILES['image']['name'];
				} else {
					if(isset($data['image']['delete']) && $data['image']['delete'] == 1) {
						$data['image'] = '';
					} else {
						unset($data['image']);
					}
				}
				
				// echo $format.'<br/>';
				// echo $data['starttime'].'<br/>';
				// echo $data['closetime'];
				
			
				 $model->setData($data)
                    ->setId($this->getRequest()->getParam('id'));
					
                if (isset($data['created_time']) && $data['created_time']) {
                    
                    $model->setCreatedTime(Mage::getModel('core/date')->gmtDate(null, $dateFrom->getTimestamp()));
                    $model->setUpdateTime(Mage::getModel('core/date')->gmtDate(null, $dateFrom->getTimestamp()));
                } else {
                    $model->setCreatedTime(Mage::getModel('core/date')->gmtDate());
					$model->setUpdateTime(Mage::getModel('core/date')->gmtDate());
                }

				// $data['closetime']; die();
                $model->save();
				// die();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('dailydeal')->__('Deal was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('dailydeal')->__('Unable to find deal to save'));
        $this->_redirect('*/*/');
    }
    
	public function savedealAction() {
		
        if ($data = $this->getRequest()->getPost()) {

			 // print_r($data);
			$links = $this->getRequest()->getPost('links');
			$format = Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM);
			$data['linkrelatedproduct'] = array();
			if (isset($links['productdeal'])) {
				$data['linkrelatedproduct'] = array_keys(Mage::helper('adminhtml/js')->decodeGridSerializedInput($links['productdeal']));
			}

			if(@$data['quantity'] < 1) $data['quantity'] = 1;
			if(@$data['save'] > 100) $data['save'] = 100;
			if(@$data['save'] <= 0) $data['save'] = 1;
			
			$closetime = Mage::app()->getLocale()->date($data['closetime'], $format)->getTimestamp();
			$starttime = Mage::app()->getLocale()->date($data['starttime'], $format)->getTimestamp();
			
			if($starttime > $closetime){
			
				Mage::getSingleton('adminhtml/session')->addError($this->__('The starttime is greater than the close time'));
				Mage::getSingleton('adminhtml/session')->setFormData($data);
				$this->_redirect('*/*/adddeal', array('id' => $this->getRequest()->getParam('id')));
				return;
			}
			
			
			if (isset($_FILES['image']['name']) and (file_exists($_FILES['image']['tmp_name']))) {
				$uploader = new Varien_File_Uploader('image');
				$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
				$uploader->setAllowRenameFiles(false);
				$uploader->setFilesDispersion(false);
				$path = Mage::getBaseDir('media') . DS . 'cmsmart/dailydeal/' ;
				$uploader->save($path, $_FILES['image']['name']);
				$data['image'] = 'cmsmart/dailydeal/'.$_FILES['image']['name'];
			} else {
				if(isset($data['image']['delete']) && $data['image']['delete'] == 1) {
					$data['image'] = '';
				} else {
					unset($data['image']);
				}
			}
			
			$this->_filterDateTime($data,array('closetime','starttime'));  

			$dateFrom = Mage::app()->getLocale()->date($data['closetime'], $format);
			if(@$data['starttime']) $data['starttime'] =  Mage::getModel('core/date')->gmtDate(null, $starttime);
			
			if(@$data['closetime']) $data['closetime'] =  Mage::getModel('core/date')->gmtDate(null, $closetime);
				
			// echo $this->getRequest()->getParam('id');			
			// print_r($data); exit();
			$productid = @$data['products_grid_radio']?$data['products_grid_radio']:$data['productid'];
            $collection = Mage::getModel('dailydeal/dailydealproducts')->getCollection()
						->addFieldToFilter('id', array('neq'=>$this->getRequest()->getParam('id')))
						->addFieldToFilter('productid', array('eq'=> $productid))
						->addFieldToFilter(array('starttime','closetime'),
						array(
								array(
									'from' => $data['starttime'],
									'to' => $data['closetime'],
									'date' => true, // specifies conversion of comparison values
								),
								array(
									'from' => $data['starttime'],
									'to' => $data['closetime'],
									'date' => true, // specifies conversion of comparison values
								)
							)
						)					
			;
			// echo $collection->getSelect().'<br/>';
			// echo $collection->getSize().'<br/>';
			// echo $data['starttime'].'<br/>';
			// echo $data['closetime'].'<br/>'; die;
			if($collection->getSize() > 0){
				Mage::getSingleton('adminhtml/session')->addWarning(Mage::helper('dailydeal')->__('Error to saved the deal. The time of deal is not valid'));
				$this->_redirect('*/*/adddeal', array('id' => $this->getRequest()->getParam('id')));
				return;
			}
			
            $model = Mage::getModel('dailydeal/dailydealproducts');

             if (isset($data['stores']) || false) { //get all children
                if ($data['stores'][0] == 0) {
                    unset($data['stores']);
                    $data['stores'] = array();
                    $stores = Mage::getSingleton('adminhtml/system_store')->getStoreCollection();
                    foreach ($stores as $store)
                        $data['stores'][] = $store->getId();
                }
            }
			
			$data['storeidlist'] = implode(',', $data['stores']);
			if(@$data['products_grid_radio']) $data['productid'] = $data['products_grid_radio'];
			     
            $model->setData($data)
                    ->setId($this->getRequest()->getParam('id'));

            try {

                //$format = Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM);
                // $format = Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
                if (isset($data['created_time']) && $data['created_time']) {
                    $dateFrom = Mage::app()->getLocale()->date($data['created_time'], $format);
                    $model->setCreatedTime(Mage::getModel('core/date')->gmtDate(null, $dateFrom->getTimestamp()));
                    $model->setUpdateTime(Mage::getModel('core/date')->gmtDate());
                } else {
                    $model->setCreatedTime(Mage::getModel('core/date')->gmtDate());
                }
				
				
                $model->save();

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('dailydeal')->__('Deal was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/adddeal', array('id' => $model->getId()));
                    return;
                }
                $this->_redirect('*/*/alldeal/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/adddeal', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('dailydeal')->__('Unable to find deal to save'));
        $this->_redirect('*/*/alldeal/');
    }

    public function deleteAction() {
        $dealId = (int) $this->getRequest()->getParam('id');
        if ($dealId) {
            try {
                // $this->_dealDelete($dealId);
				$model = Mage::getModel('dailydeal/dailydeal')->load($dealId);
				$model->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Deal was successfully deleted'));
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }
	
    public function deletedealAction() {
        $dealId = (int) $this->getRequest()->getParam('id');
        if ($dealId) {
            try {
				$model = Mage::getModel('dailydeal/dailydealproducts')->load($dealId);
				$model->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Deal was successfully deleted'));
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }

    public function massDeleteAction() {
		
        $dailydealIds = $this->getRequest()->getParam('dailydeal');
        if (!is_array($dailydealIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select deal(s)'));
        } else {
            try {
                foreach ($dailydealIds as $dealId) {
                    //$this->_dealDelete($dealId);
					$model = Mage::getModel('dailydeal/dailydeal')->load($dealId);
					$model->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('adminhtml')->__(
                                'Total of %d record(s) were successfully deleted', count($dailydealIds)
                        )
                );
            } catch (Exception $e) {
				echo $e->getMessage();
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function massAlldeleteAction() {
        $dailydealIds = $this->getRequest()->getParam('dailydeal');
        if (!is_array($dailydealIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select deal(s)'));
        } else {
            try {
                foreach ($dailydealIds as $dealId) {
					$model = Mage::getModel('dailydeal/dailydealproducts')->load($dealId);
					$model->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('adminhtml')->__(
                                'Total of %d record(s) were successfully deleted', count($dailydealIds)
                        )
                );
            } catch (Exception $e) {
				echo $e->getMessage();
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/alldeal');
    }


    public function massStatusAction() {
        $dailydealIds = $this->getRequest()->getParam('dailydeal');
        if (!is_array($dailydealIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select deal(s)'));
        } else {
            try {

                foreach ($dailydealIds as $dailydealId) {
                    $dailydeal = Mage::getModel('dailydeal/dailydeal')
                            ->load($dailydealId)
                            ->setStatus($this->getRequest()->getParam('status'))
                            ->setStores('')
                            ->setIsMassupdate(true)
                            ->save();
                }
                $this->_getSession()->addSuccess(
                        $this->__('Total of %d record(s) were successfully updated', count($dailydealIds))
                );
            } catch (Exception $e) {

                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    protected function displayTitle($data = null, $root = 'New') {
        return $this;
    }

}
