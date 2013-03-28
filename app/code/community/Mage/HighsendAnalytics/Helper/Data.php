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
 * @package    Mage_HighsendAnalytics_Piwik
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
    public function isPiwikAnalyticsAvailable($store = null)
    {
        $siteId = Mage::getStoreConfig(self::XML_PATH_SITE, $store);
		//$installPath = Mage::getStoreConfig(self::XML_PATH_INSTALL, $store);
        //return $siteId && $installPath && Mage::getStoreConfigFlag(self::XML_PATH_ACTIVE, $store);
		return $siteId && Mage::getStoreConfigFlag(self::XML_PATH_ACTIVE, $store);
	}
}
