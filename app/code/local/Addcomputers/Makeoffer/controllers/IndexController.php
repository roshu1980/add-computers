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

		// if the user is not logged in, just get out!
		if(!Mage::getSingleton('customer/session')->isLoggedIn())
			return $this->getResponse()->setBody(json_encode(array('success' => false, 'error' => 'notLoggedIn')));

		$countIncresed = 0;
		// get customer data
		$customer = Mage::getSingleton('customer/session')->getCustomer();
		$oldOffers = $customer->getOffers();
		$currentDate = date('Y-m-d');
		$newOffersArr = array($currentDate => array());
		if ($oldOffers && $oldOffers != '')
		{
			$oldOffersArr = json_decode($oldOffers, true);

			// first remove all the expired offers from the customer offer list
			if (array_key_exists($currentDate, $oldOffersArr))
				$newOffersArr[$currentDate] = $oldOffersArr[$currentDate];

			// the customer already made 3 offers for this product? --> over and out!
			if (isset($newOffersArr[$currentDate][$postParams['id']]) && count($newOffersArr[$currentDate][$postParams['id']]) > 2)
				return $this->getResponse()->setBody(json_encode(array('success' => false, 'error' => 'has3Offers')));

			// else increase the offers number and save it
			$newOffersArr[$currentDate][$postParams['id']][] = $postParams['price'];
			$customer->setOffers(json_encode($newOffersArr));
			$customer->save();
			$countIncreased = 1;
		}

		// check if the product allows offers
		$_product = Mage::getModel('catalog/product')->load($postParams['id']);
		if (!is_numeric($_product->getMinPriceLimit()) || $_product->getMinPriceLimit() <= 0 || $_product->getMinPriceLimit() > $_product->getPrice() )
			return $this->getResponse()->setBody(json_encode(array('success' => false, 'error' => 'productError')));

		// if we didnt increase the offers above, do it now !
		if (!$countIncreased)
		{
			$newOffersArr[$currentDate][$postParams['id']][] = $postParams['price'];
			$customer->setOffers(json_encode($newOffersArr));
			$customer->save();
		}

		// if the user is trolling(added a price bigger than the actual price) --> over and out!
		if ($postParams['price'] > $_product->getPrice())
			return $this->getResponse()->setBody(json_encode(array('success' => false, 'error' => 'priceTooBig')));
		
		//check if the offer is acceptable
		if (!($postParams['price'] >= $_product->getMinPriceLimit() && $postParams['price'] <= $_product->getPrice()))
		{
	    	$this->sendOfferEmail($postParams);
			return $this->getResponse()->setBody(json_encode(array('success' => false, 'error' => 'offerRejected')));	
		}

		// is product in stock ?
		if (!$_product->isInStock())
			return $this->getResponse()->setBody(json_encode(array('success' => false, 'error' => 'productNotInStock')));	

		//all ok ? save the product, price and qty in session and send the good news :)
		$postParams['quantity'] = ($_product->getStockItem()->getQty() < $postParams['quantity']) ? $_product->getStockItem()->getQty() : $postParams['quantity'];
		Mage::getSingleton( 'customer/session' )->setData('userOffer', $postParams);

		return $this->getResponse()->setBody(json_encode(array('success' => true)));
    }

    public function addtocartAction()
    {
		$this->getResponse()->setHeader('Content-type', 'application/json');

		// if the user is not logged in, remove the session and just get out!
		if(!Mage::getSingleton('customer/session')->isLoggedIn())
		{
			Mage::getSingleton( 'customer/session' )->unsetData('userOffer');
			return $this->getResponse()->setBody(json_encode(array('success' => false, 'error' => 'notLoggedIn')));
		}

    	// get offer from session
        $userOffer = Mage::getSingleton( 'customer/session' )->getData('userOffer');
        
        // check if we have the correct product ID
        if (!isset($userOffer['id']) || !is_numeric($userOffer['id']) || $userOffer['id'] < 0)
			return $this->getResponse()->setBody(json_encode(array('success' => false, 'error' => 'productError')));

		// load the product
		$product = Mage::getModel('catalog/product')->load($userOffer['id']);

		// is product in stock ?
		if (!$product->isInStock())
			return $this->getResponse()->setBody(json_encode(array('success' => false, 'error' => 'productError')));

		// check the price offered
		if (!($userOffer['price'] >= $product->getMinPriceLimit() && $userOffer['price'] <= $product->getPrice()))
			return $this->getResponse()->setBody(json_encode(array('success' => false, 'error' => 'productError')));

		// now we can add the product to cart
		$cart = Mage::getModel('checkout/cart');
		$cart->init();
		$params = array(
		    'product' => $product->getId(), // This would be $product->getId()
		    'qty' => $userOffer['quantity']
		);      

		try {   
			// first write in session that this is a special add to cart, then send it to the observer ;)
			$userOffer['makeOffer'] = 1;
			Mage::getSingleton( 'customer/session' )->setData('userOffer', $userOffer);

		    $cart->addProduct($product, new Varien_Object($params));
		    Mage::getSingleton('checkout/session')->setCartWasUpdated(true);
		    $cart->save();

			// send the good news
			return $this->getResponse()->setBody(json_encode(array('success' => true)));
		}
		catch (Exception $ex) {
			return $this->getResponse()->setBody(json_encode(array('success' => false, 'error' => 'productError')));
		}    	
	}

    public function sendOfferEmail($userOffer)
    {
		// get customer data
		$customer = Mage::getSingleton('customer/session')->getCustomer();

		$emailTemplate = Mage::getModel('core/email_template')->loadDefault('makeoffer');

		$name_from = Mage::getStoreConfig('trans_email/ident_general/name');
		$email_from = Mage::getStoreConfig('trans_email/ident_general/email');
		$name_to = Mage::getStoreConfig('trans_email/ident_sales/name');
		$email_to = Mage::getStoreConfig('trans_email/ident_sales/email');

		// get the customer data
		$emailTemplateVariables = array();
		$emailTemplateVariables['name'] = $customer->getName();
		$emailTemplateVariables['email'] = $customer->getEmail();

		// get the product data
		$product = Mage::getModel('catalog/product')->load($userOffer['id']);
		$emailTemplateVariables['prod_id'] = $product->getId();
		$emailTemplateVariables['prod_name'] = $product->getName();
		$emailTemplateVariables['prod_qty'] = $userOffer['quantity'];
		$emailTemplateVariables['prod_price'] = $userOffer['price'];

		//Appending the Custom Variables to Template.
		$processedTemplate = $emailTemplate->getProcessedTemplate($emailTemplateVariables);

		//Sending E-Mail to Sales.
		$mail = Mage::getModel('core/email')
		 ->setSubject('[serversandspares.com] - Offer submission')
		 ->setToName($name_to)
		 ->setToEmail($email_to)
		 ->setFromName($name_from)
		 ->setFromEmail($email_from)
		 ->setBody($processedTemplate)
		 ->setType('html');
		 try{
			 //Confimation E-Mail Send
			 $mail->send();
		 }
		 catch(Exception $error)
		 {
		 	// die silently
			 return false;
		 }
    }
}

