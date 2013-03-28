<?php
/**
 *
 * HighSend + Piwik Extension for Magento created by Leroy Ware
 * Extends Piwik Extension for Magento created by Adrian Speyer (https://github.com/adriansonline/Piwik-for-Magento)
 * Get Piwik at http://www.piwik.org - Open source web analytics
 * Sign up for HighSend at http://www.highsend.com
 *
 * @category    Mage
 * @package     Mage_HighsendAnalytics_Model_Sdk
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */
class Mage_HighsendAnalytics_Model_Sdk
{

  public function subscribe($data) {
	$data["key"] = Mage::getStoreConfig(Mage_HighsendAnalytics_Helper_Data::XML_PATH_HSKEY);
    $url = "https://highsend.com/api/lists/subscribe/";
	return $this->_curlPost($data, $url);
  }
  
  // a couple curl functions to make life easier...
  public function _curlPost($data, $url) {
	$fields_string = ""; 
    foreach($data as $k=>$v) { 
	   $fields_string .= $k.'='.$v.'&';
    }
    rtrim($fields_string,'&');
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_POST,count($this));
    curl_setopt($ch,CURLOPT_POSTFIELDS,$fields_string);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $content = curl_exec($ch);
    curl_close($ch);
    return $content;
  }

}