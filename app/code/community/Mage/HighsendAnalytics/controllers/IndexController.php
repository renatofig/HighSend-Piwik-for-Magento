<?php
/**
 *
 * HighSend + Piwik Extension for Magento created by Leroy Ware
 * Extends Piwik Extension for Magento created by Adrian Speyer (https://github.com/adriansonline/Piwik-for-Magento)
 * Get Piwik at http://www.piwik.org - Open source web analytics
 * Sign up for HighSend at http://www.highsend.com
 *
 * @category    Mage
 * @package     Mage_HighsendAnalytics_Controller_IndexController
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */

class Mage_HighsendAnalytics_IndexController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {

      $this->loadLayout();
		
      $active = Mage::getStoreConfig(Mage_HighsendAnalytics_Helper_Data::XML_PATH_ACTIVE);
	  $siteId = Mage::getStoreConfig(Mage_HighsendAnalytics_Helper_Data::XML_PATH_SITE); 
	  $installPath = Mage::getStoreConfig(Mage_HighsendAnalytics_Helper_Data::XML_PATH_INSTALL); 
	  $pwtoken= Mage::getStoreConfig(Mage_HighsendAnalytics_Helper_Data::XML_PATH_PWTOKEN);
		
	  $hskey = Mage::getStoreConfig(Mage_HighsendAnalytics_Helper_Data::XML_PATH_HSKEY); 
      $hslist = Mage::getStoreConfig(Mage_HighsendAnalytics_Helper_Data::XML_PATH_HSLIST);

       if (!empty($pwtoken) && !empty($hskey)){
		   $block = $this->getLayout()->createBlock('core/text', 'highsend-block')->setText('<iframe src="'.$installPath.'/index.php?module=Widgetize&action=iframe&moduleToWidgetize=Dashboard&actionToWidgetize=index&idSite='.$siteId.'&period=week&date=yesterday&token_auth='.$pwtoken.'" frameborder="0" marginheight="0" marginwidth="0" width="100%" height="1000px"></iframe>');
		   $this->_addContent($block);
		   $this->_setActiveMenu('highsend_menu')->renderLayout();
	   }	   
	  	   
	   if (empty($pwtoken) || empty($hskey)){ 
		  $block = $this->getLayout()->createBlock('core/text', 'highsend-block')->setText('Piwik Token Auth Key and HighSend Key are required.');
		   $this->_addContent($block);
		   $this->_setActiveMenu('highsend_menu')->renderLayout();
	   }
	   	
    }
	
}
