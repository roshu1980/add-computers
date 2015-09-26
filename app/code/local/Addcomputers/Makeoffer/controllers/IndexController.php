<?php
class Addcomputers_Makeoffer_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
    }

    public function sendofferAction()
    {
		$postParams = $this->getRequest()->getParams();
		$this->getResponse()->setHeader('Content-type', 'application/json');

		if(!Mage::getSingleton('customer/session')->isLoggedIn())
			return $this->getResponse()->setBody(json_encode(array('success' => false, 'error' => 'notLoggedIn')));

		// get customer data
		$customer = Mage::getSingleton('customer/session')->getCustomer();
		$oldOffers = $customer->getOffers();
		if ($oldOffers && $oldOffers != '')
		{
			$oldOffersArr = json_decode($oldOffers);

			// first remove all the expired offers from the customer offer list
			$newOffersArr = array();
			$currentDate = date('Y-m-d');
			foreach ($oldOffersArr as $date => $offers)
				if ($date == $currentDate)
					$newOffersArr[$currentDate][] = $offers;

			// the customer already made 3 offers for this product? --> over and out!
			if (isset($newOffersArr[$currentDate][$postParams['id']]) && count($newOffersArr[$currentDate][$postParams['id']]) > 2)
				return $this->getResponse()->setBody(json_encode(array('success' => false, 'error' => 'has3Offers')));

			// else increase the offers number and save it
			$newOffersArr[$currentDate][$postParams['id']][] = 1;
			$customer->setOffers(json_encode($newOffersArr));
			$customer->save();
		}

		// check if the product allows offers
		$_product = Mage::getModel('catalog/product')->load($postParams['id']);
		if (!is_numeric($_product->getMinPriceLimit()) || $_product->getMinPriceLimit() <= 0 || $_product->getMinPriceLimit() > $_product->getPrice() )
			return $this->getResponse()->setBody(json_encode(array('success' => false, 'error' => 'productError')));
		
		
    }
}