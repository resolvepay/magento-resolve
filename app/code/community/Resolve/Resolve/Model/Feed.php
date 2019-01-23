<?php

class Resolve_Resolve_Model_Feed extends Mage_AdminNotification_Model_Feed
{
    const XML_FEEDS_PATH        = 'payment/resolve/notification_feed';
    const XML_FEEDS_SUBSCRIBED  = 'payment/resolve/notification_update';

    protected function _isNotificationSubscribed()
    {
        return Mage::getStoreConfig(self::XML_FEEDS_SUBSCRIBED) == 1;
    }

    public function getFeedUrl()
    {
        if ($this->_feedUrl === null) {
            $this->_feedUrl = Mage::getStoreConfig(self::XML_FEEDS_PATH);
        }
        return $this->_feedUrl;
    }

    public function checkUpdate()
    {
        if ($this->_isNotificationSubscribed()) {
            if (($this->getFrequency() + $this->getLastUpdate()) > time()) {
                return $this;
            }

            if (!extension_loaded('curl')) {
                return $this;
            }

            $inbox = Mage::getModel('adminnotification/inbox');

            //$feed = Mage::getStoreConfig(self::XML_FEEDS_PATH);
            //$this->_feedUrl = $feed;
            $feedData = array();

            $feedXml = $this->getFeedData();
            if ($feedXml && $feedXml->entry) {
                foreach ($feedXml->entry as $item) {
                    $feedData[] = array(
                        'severity' =>
                            (int)isset($item->severity) ? $item->severity
                                : Mage_AdminNotification_Model_Inbox::SEVERITY_NOTICE,
                        'date_added' => $this->getDate((string)$item->updated),
                        'title' => 'Resolve Extension Version ' . (string)$item->title . ' is now available',
                        'description' => 'Resolve Extension Version ' . (string)$item->title . ' for Magento is now available for download and upgrade. To see a full list of updates please check the release notes page.',
                        'url' => 'https://github.com/Resolve/Magento_Resolve/releases', // Resolve Magento 1 extension github releases
                    );
                    break; // we only need latest release version notification
                }
                if ($feedData) {
                    $inbox->parse(array_reverse($feedData));
                }
            }
            $this->setLastUpdate();
            return $this;
        }
    }

    /**
     * Retrieve Last update time
     *
     * @return int
     */
    public function getLastUpdate()
    {
        return Mage::app()->loadCache('resolve_admin_notifications');
    }

    /**
     * Set last update time (now)
     *
     * @return Mage_AdminNotification_Model_Feed
     */
    public function setLastUpdate()
    {
        Mage::app()->saveCache(time(), 'resolve_admin_notifications');
        return $this;
    }
}
