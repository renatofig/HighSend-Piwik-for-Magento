<?php
/**
 *
 * HighSend + Piwik Extension for Magento created by Leroy Ware
 * Extends Piwik Extension for Magento created by Adrian Speyer (https://github.com/adriansonline/Piwik-for-Magento)
 * Get Piwik at http://www.piwik.org - Open source web analytics
 * Sign up for HighSend at http://www.highsend.com
 *
 * @category    Mage
 * @package     Mage_HighsendAnalytics_Model_Observer
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */


/**
 * HighSend module observer
 *
 * @category   Mage
 * @package    Mage_Highsend
 */
class Mage_HighsendAnalytics_Model_Observer
{
   

    /**
     * Add order information into Piwik block to render on checkout success pages
     *
     * @param Varien_Event_Observer $observer
     */
    public function setPiwikAnalyticsOnOrderSuccessPageView(Varien_Event_Observer $observer)
    {
        $orderIds = $observer->getEvent()->getOrderIds();
        if (empty($orderIds) || !is_array($orderIds)) {
            return;
        }
        $block = Mage::app()->getFrontController()->getAction()->getLayout()->getBlock('highsend_analytics');
        if ($block) {
            $block->setOrderIds($orderIds);
        }
    }


   
}
   
   