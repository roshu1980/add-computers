<?php
class Cmsmart_Dailydeal_IndexController extends Mage_Core_Controller_Front_Action
{
	public $CF = array();
	public function indexAction(){
		// echo $this->getPathInfo();
		// print_r(get_class_methods($this));
		//echo Mage::app()->getFrontController()->getRequest()->getRouteName();
		
		$this->loadLayout();
		$routeName =  $this->getRequest()->getRouteName();
		$dealcf = array();
		$dealcf['dealtype'] = 1;
		$dealcf['default'] = Mage::getStoreConfig('dailydeal');
		
		$this->getLayout()->getBlock('head')->setTitle('Today deal Title');
		switch($routeName){
			case 'dailydeal':
				$dealcf['dealtype'] = 2;
				$this->getLayout()->getBlock('head')->setTitle('Daily deal Title');
				break;
			
			case 'upcomingdeal':
				$dealcf['dealtype'] = 3;
				$this->getLayout()->getBlock('head')->setTitle('Upcoming deal Title');
				break;
			
			case 'misseddeal':
				$dealcf['dealtype'] = 4;
				$this->getLayout()->getBlock('head')->setTitle('Missed deal Title');
				break;
		}
		
		Mage::register('dealcf', $dealcf); // 1 as today deal, 2 dailydeal, 3 comming deal, 4 expired deal
		
		// $this->getLayout()->getBlock('head')
			// ->setTitle('Daily deal shop')
			// ->setKeywords('Daily deal shop')
			// ->setDescription('Daily deal shop');
			
		$this->renderLayout();
	}

}