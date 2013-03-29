<?php
/**
 *
 * HighSend + Piwik Extension for Magento created by Leroy Ware
 * Extends Piwik Extension for Magento created by Adrian Speyer (https://github.com/adriansonline/Piwik-for-Magento)
 * Get Piwik at http://www.piwik.org - Open source web analytics
 * Sign up for HighSend at http://www.highsend.com
 *
 * PiwikAnalytics Page Block
 *
 * @category   Mage
 * @package    Mage_HighsendAnalytics_Helper_Data
 * @license     @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */



class Mage_HighsendAnalytics_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Config paths for using throughout the code
     */
    const XML_PATH_ACTIVE  = 'highsend/analytics/active';
    const XML_PATH_SITE =    'highsend/analytics/site';
	const XML_PATH_INSTALL = 'highsend/analytics/install';
	const XML_PATH_PWTOKEN = 'highsend/analytics/pwtoken';
	
	const XML_PATH_HSKEY   = 'highsend/settings/api_key';
	const XML_PATH_HSLIST  = 'highsend/settings/list_name';
	
	
    /**
     *
     * @param mixed $store
     * @return bool
     */
    public function isPiwikAnalyticsAvailable($store = null) {
        $siteId = Mage::getStoreConfig(self::XML_PATH_SITE, $store);
		//$installPath = Mage::getStoreConfig(self::XML_PATH_INSTALL, $store);
        //return $siteId && $installPath && Mage::getStoreConfigFlag(self::XML_PATH_ACTIVE, $store);
		return $siteId && Mage::getStoreConfigFlag(self::XML_PATH_ACTIVE, $store);
	}
	
	/**
     * Check the customer's newsletter optin status
     * @return string
     */
	public function isCustomerSubscribed($email) {
	    $isSubscribed = 0;
		$db = Mage::getSingleton('core/resource')->getConnection('core_read');
		$sql = "SELECT * FROM `newsletter_subscriber` WHERE `subscriber_email`='".$email."' AND `subscriber_status`='1' LIMIT 1";
		$result = $db->fetchAll($sql);
		if($result){
			$isSubscribed = 1;
        }
		return $isSubscribed;	
	}
	
	public function saveCustomerToHighsend() {
	   
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
	   }

	   try {
		  $HighSend = Mage::getSingleton('highsendanalytics/sdk');
		  $HighSend->subscribe($data);
	   } catch(Exception $e){
		   // handle any errors here   
	   }
	   	
	}
	
	
}
