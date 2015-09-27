<?php

class Addcomputers_Makeoffer_Model_Observer
{
    public function updatePrice($observer) 
    {
        // get the data from session
        $userOffer = Mage::getSingleton( 'customer/session' )->getData('userOffer');
        
        // if this is not a "make Offer" add to cart --> get out!
        if (!isset($userOffer['makeOffer']) || $userOffer['makeOffer'] != 1)
            return true;

        // add product to cart
        $event = $observer->getEvent();
        $quote_item = $event->getQuoteItem();
        $quote_item->setOriginalCustomPrice($userOffer['price']);
        $quote_item->save();

        // finally unset the sessions info
        Mage::getSingleton( 'customer/session' )->unsetData('userOffer');
    }
}