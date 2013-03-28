<?php
/**
 *
 * HighSend + Piwik Extension for Magento created by Leroy Ware
 * Extends Piwik Extension for Magento created by Adrian Speyer (https://github.com/adriansonline/Piwik-for-Magento)
 * Get Piwik at http://www.piwik.org - Open source web analytics
 * Sign up for HighSend at http://www.highsend.com
 *
 * HighsendAnalytics Page Block
 *
 * @category   Mage
 * @package    Mage_HighsendAnalytics_Highsend
 * @license     @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */
 
class Mage_HighsendAnalytics_Block_Highsend extends Mage_Core_Block_Template
{
 	
	    /**
     * Get a specific page name (may be customized via layout)
     *
     * @return string|null
     */
    public function getPageName()
    {
        return $this->_getData('page_name');
    }


    /**
     * Render information about specified orders and their items
     * http://piwik.org/docs/ecommerce-analytics/
	 */
    protected function _getOrdersTrackingCode()
    {
        $orderIds = $this->getOrderIds();
        if (empty($orderIds) || !is_array($orderIds)) {
            return;
        }

        $collection = Mage::getResourceModel('sales/order_collection')
            ->addFieldToFilter('entity_id', array('in' => $orderIds))
        ;
        $result = array();
     
	 foreach ($collection as $order) {
	 foreach ($order->getAllVisibleItems() as $item) 
	  {
	  
		//get category name
	$product_id = $item->product_id;
    $_product = Mage::getModel('catalog/product')->load($product_id);
    $cats = $_product->getCategoryIds();
    $category_id = $cats[0]; // just grab the first id
    $category = Mage::getModel('catalog/category')->load($category_id);
    $category_name = $category->getName();
	  
	  
            if ($item->getQtyOrdered()) {
               $qty = number_format($item->getQtyOrdered(), 0, '.','');
            } else {
               $qty = '0';
            }
	    $result[] = sprintf("piwikTracker.addEcommerceItem( '%s', '%s', '%s', %s, %s);",
                    $this->jsQuoteEscape($item->getSku()), 
					$this->jsQuoteEscape($item->getName()),
                    $category_name, 
                    $item->getBasePrice(), 
					$qty
                );
       				
}
	foreach ($collection as $order)
	 	 	 {
            if ($order->getGrandTotal()) {
               $subtotal= $order->getGrandTotal() - $order->getShippingAmount() - $order->getShippingTaxAmount();
            } else {
               $subtotal= '0.00';
            } 
		 $result[] = sprintf("piwikTracker.trackEcommerceOrder( '%s', %s, %s, %s, %s);",
                $order->getIncrementId(),
                $order->getBaseGrandTotal(),
				$subtotal,
                $order->getBaseTaxAmount(),
                $order->getBaseShippingAmount()
            );	

			
            }
        }
        return implode("\n", $result);
    }
		
		
	/**
     * Render information when cart updated
     * http://piwik.org/docs/ecommerce-analytics/
	 */
    protected function _getEcommerceCartUpdate() {
	
		$cart = Mage::getModel('checkout/cart')->getQuote()->getAllVisibleItems();
	
		foreach($cart as $cartitem) {
		
		//get category name
		$product_id = $cartitem->product_id;
		$_product = Mage::getModel('catalog/product')->load($product_id);
		$cats = $_product->getCategoryIds();
		if (isset($cats)){$category_id = $cats[0];} // just grab the first id
		$category = Mage::getModel('catalog/category')->load($category_id);
		$category_name = $category->getName();
		$nameofproduct = $cartitem->getName();
		$nameofproduct = str_replace('"', "", $nameofproduct);
		
		if ($cartitem->getPrice() == 0 || $cartitem->getPrice() < 0.00001):
		continue;
	    endif;
		echo 'piwikTracker.addEcommerceItem("'.$cartitem->getSku().'","'.$nameofproduct.'","'.$category_name.'",'.$cartitem->getPrice().','.$cartitem->getQty().');';
		
		echo "\n";
		}
		
		//total in cart
		$grandTotal = Mage::getModel('checkout/cart')->getQuote()->getGrandTotal();
		if ($grandTotal == 0) echo ''; else
		echo 'piwikTracker.trackEcommerceCartUpdate('.$grandTotal.');';
		echo "\n";
		
		$this->_saveUserToHighSend(); // added by HighSend
	}		

		
	/**
     * Render information when product page view
     * http://piwik.org/docs/ecommerce-analytics/
	 */
    protected function _getProductPageview() {

		$currentproduct = Mage::registry('current_product');
		
		if (!($currentproduct instanceof Mage_Catalog_Model_Product)) {
				return;
			}
		
		$product_id = $currentproduct->getId();
		$_product = Mage::getModel('catalog/product')->load($product_id);
		$cats = $_product->getCategoryIds();
		$category_id = $cats[0]; // just grab the first id
		$category = Mage::getModel('catalog/category')->load($category_id);
		$category_name = $category->getName();
		$product = $currentproduct->getName();
		$product = str_replace('"', "", $product);
		
		
		echo 'piwikTracker.setEcommerceView("'.$currentproduct->getSku().'", "'.$product.'","'.$category_name.'",'.$currentproduct->getPrice().');';
		
		echo "\n";
		
		$this->_saveUserToHighSend(); // added by HighSend

		Mage::unregister('current_category');
	}	
	

	/**
     * Render information of category view
     * http://piwik.org/docs/ecommerce-analytics/
	*/	 
	protected function _getCategoryPageview() {
		$currentcategory = Mage::registry('current_category');
		
		if (!($currentcategory instanceof Mage_Catalog_Model_Category)) {
				return;
			}
		echo 'piwikTracker.setEcommerceView(false,false,"'.$currentcategory->getName().'");';		
		echo "\n";
		
		$this->_saveUserToHighSend(); // added by HighSend
		
		Mage::unregister('current_product');	
	}	
	
	
	
	/**
     * Render email address of logged in user
	 * Uses a visit-scope custom variable to store logged in user's email address
	*/	 
	protected function _getUser() {
		
		if (Mage::getSingleton('customer/session')->isLoggedIn()) {  
		   $email = Mage::getSingleton('customer/session')->getCustomer()->getEmail();	  
		   echo 'piwikTracker.setCustomVariable (1, "User", "'.$email.'", scope = "visit");';   
		}
		
	}	
	
	/**
     * Render HighSend list
	 * Uses a visit-scope custom variable to store HighSend list name
	*/	 
	protected function _getList() {	
	
		$list_name = Mage::getStoreConfig(Mage_HighsendAnalytics_Helper_Data::XML_PATH_HSLIST);
		if (Mage::getSingleton('customer/session')->isLoggedIn() && $list_name) {	  
			echo 'piwikTracker.setCustomVariable (2, "List", "'.$list_name.'", scope = "visit");';
		}
		   
	}
			
	
		
    /**
     * Render Piwik tracking scripts
     *
     * @return string
     */
    protected function _toHtml() {
        if (!Mage::helper('highsendanalytics')->isPiwikAnalyticsAvailable()) {
            return '';
        }

        return parent::_toHtml();
    }
	
	/**
     * Check the customer's newsletter optin status
     * Added by HighSend
     * @return string
     */
	protected function _isSubscribed() {
	    $isSubscribed = 0;
		$db = Mage::getSingleton('core/resource')->getConnection('core_read');
		$sql = "SELECT * FROM `newsletter_subscriber` WHERE `subscriber_email`='".Mage::getSingleton('customer/session')->getCustomer()->getEmail()."' AND `subscriber_status`='1' LIMIT 1";
		$result = $db->fetchAll($sql);
		if($result){
			$isSubscribed = 1;
        }
		return $isSubscribed;	
	}
	
	/**
     * Save the customer to HighSend. Saved fields: first_name, last_name, email, billing address (if provided), optin status
     * Added by HighSend
     */
	
	protected function _saveUserToHighSend() {
	   
	   $data = array();	  			   
	   $customer = Mage::getSingleton('customer/session')->getCustomer();
	   $data["first_name"] = $customer->getData("firstname");
	   $data["last_name"] = $customer->getData("lastname");
	   $data["email"] = $customer->getData("email");
	   $data["list_name"] = Mage::getStoreConfig(Mage_HighsendAnalytics_Helper_Data::XML_PATH_HSLIST);
	  
	   $customerAddressId = Mage::getSingleton('customer/session')->getCustomer()->getDefaultBilling();
	   
	   if ($customerAddressId) {
		   $address = Mage::getModel('customer/address')->load($customerAddressId);			   
		   $data["address_1"] = substr($address->getData("street"), 0, stripos($address->getData("street"), "\n"));
		   $data["address_2"] = substr($address->getData("street"), stripos($address->getData("street"), "\n")+1);
		   $data["city"] = $address->getData("city");
		   $data["state"] = $address->getData("region");
		   $data["postal"] = $address->getData("postcode");
		   $data["country"] = $address->getData("country_id");
		   $data["phone"] = $address->getData("telephone");
		   $data["optin"] = $this->_isSubscribed();
	   }

	   try {
		  $HighSend = Mage::getSingleton('highsendanalytics/sdk');
		  $HighSend->subscribe($data);
	   } catch(Exception $e){
		   // handle any errors here   
	   }
	   	
	}
	
}
