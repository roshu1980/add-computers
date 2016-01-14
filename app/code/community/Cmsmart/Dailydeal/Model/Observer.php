<?php
class Cmsmart_Dailydeal_Model_Observer {
    public function collectionloadafter($observer) {
        $productCollection = $observer['collection'];
        foreach ($productCollection as $product){
		
			if($product->getSave()){
				$product->setData('final_price', $product->getPrice() - $product->getSave() * $product->getPrice() / 100);
			}else{
			
				$dailydeal = Mage::getModel('dailydeal/dailydealproducts')->getActiveDealByProduct($product->getEntityId());
				
				if($dailydeal->getProductid()){
					$temp = $dailydeal->getQuantity() - $dailydeal->getSold();
					$product->setData('final_price',$product->getPrice() - $dailydeal->getSave()*$product->getPrice()/100);
				}
				
			}
        }
    }
	
    public function getfinalprice($observer) {
        $product = $observer['product'];
		$dailydeal = Mage::getModel('dailydeal/dailydealproducts')->getActiveDealByProduct($product->getEntityId(), true);
		if($dailydeal->getId()){
			$temp = $dailydeal->getQuantity() - $dailydeal->getSold();
			$product->setData('final_price', $product->getPrice() - $dailydeal->getSave()*$product->getPrice()/100);
		}
    }

	
    public function update_items($observer){
        $cart  = $observer['cart'];
        $items = $cart->getQuote()->getAllItems();
        //$temp = Mage::getStoreConfig('dailydeal/general/limit');
        $i = 0;
		
        foreach ($items as $item) {
            $dailydeal = Mage::getModel('dailydeal/dailydealproducts')->getActiveDealByProduct($item->getProductId());
			if($dailydeal->getProductid()){
				$limit = $dailydeal->getQuantity() - $dailydeal->getSold();
				
				if($limit > 0){
					// if (($limit > $temp)&&($temp > 0)) $limit = $temp;
					if ($item->getQty() > $limit) {
						$item->setQty($limit)->save();
						$i = 1;
					}
				}
			}
        }
		
        if ($i == 1) Mage::getSingleton('checkout/session')->addError(Mage::helper('dailydeal')->__('The number that you have inserted is over the deal quantity left. Please reinsert another one!'));
    }
	
    public function addproduct(){
        $cart = $this->_getCart();
        $items = $cart->getQuote()->getAllItems();
        $productId = (int)Mage::app()->getRequest()->getParam('product');
        
                $dailydeal = Mage::getModel('dailydeal/dailydealproducts')->getActiveDealByProduct($productId);
				if($dailydeal->getProductid()){
                $limit = $dailydeal->getQuantity() - $dailydeal->getSold();
                if($limit>0){
                    $temp = Mage::getStoreConfig('dailydeal/general/limit');
                    if (($limit > $temp)&&($temp>0)) $limit = $temp;
                    $qty=1;
                    $is_order = false;
                    if (Mage::app()->getRequest()->getParam('qty')) $qty = Mage::app()->getRequest()->getParam('qty');
                    
                    foreach ($items as $item){
                        if ($item->getProductId() == $productId) {
                            $is_order = true;
                            if (($item->getQty() + $qty) > $limit) {
                                Mage::app()->getRequest()->setPost('qty',0);
                                $item->setQty($limit-1)->save();
                                Mage::getSingleton('checkout/session')->addError(Mage::helper('dailydeal')->__('The number that you have inserted is over the deal quantity left. Please reinsert another one!'));
                            }
                        }                         
                    }
                    if ((!$is_order)&&($qty > $limit )){
                                Mage::app()->getRequest()->setPost('qty',$limit);
                                Mage::getSingleton('checkout/session')->addError(Mage::helper('dailydeal')->__('The number that you have inserted is over the deal quantity left. Please reinsert another one!'));
                    }
                }
				}
    }
	
    public function saveorder($observer) {
        $order = $observer['order'];
        $items = $order->getAllItems();
        $deals = array();
        foreach($items as $item) {
            $productId = $item->getProductId();
            $dailydeal = Mage::getModel('dailydeal/dailydealproducts')->getActiveDealByProduct($productId);
			if($dailydeal->getProductid()){
				$temp = $dailydeal->getQuantity() - $dailydeal->getSold();
				$sold = $dailydeal->getSold();
				
				if ($temp>0){
					$deals[] = $dailydeal->getId();
					$dailydeal->setSold($sold + $item->getQtyOrdered())->save();
				}
			}
        }
        $order->setData('dailydeals', implode(",",$deals));
    }	
	
    protected function _getCart(){
        return Mage::getSingleton('checkout/cart');
    }
	
    public function qtyItem($items, $product_id, $check){
        $qty = 0;
        foreach ($items as $item) {
            if($product_id = $item->getProductId()){
			
                if($check == 1){
					$qty = $item->getQtyCanceled();
                }  else {
					$qty = $item->getQty();    
                }
				
				return $qty;
            }
        }
        return $qty;
    }
	
    public function orderCancelAfter($observer){
       $order = $observer['order'];
       $dailydeals = $order->getDailydeals();
        $items = $order->getAllItems();
		$dailydeals_arr = explode(',', $dailydeals);
		foreach($dailydeals_arr as $value){
			$dailydeal = Mage::getModel('dailydeal/dailydealproducts')->load($value);
			$product_id = $dailydeal->getProductid();
			$qty = $this->qtyItem($items, $product_id, 1);
			$sold = $dailydeal->getSold() - $qty;
			if($sold >= 0){
				$dailydeal->setSold($sold)->save();			
			}
			
		}
    }
}