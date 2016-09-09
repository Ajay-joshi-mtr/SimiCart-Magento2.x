<?php

namespace Simi\Simiconnector\Model;

/**
 * Connector Model
 *
 * @method \Simi\Simiconnector\Model\Resource\Page _getResource()
 * @method \Simi\Simiconnector\Model\Resource\Page getResource()
 */
class Siminotification extends \Magento\Framework\Model\AbstractModel
{
    /**
     * @var \Simi\Simiconnector\Helper\Website
     **/
    protected $_websiteHelper;


    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param ResourceModel\Key $resource
     * @param ResourceModel\Key\Collection $resourceCollection
     * @param \Simi\Simiconnector\Helper\Website $websiteHelper
     * @param AppFactory $app
     * @param PluginFactory $plugin
     * @param DesignFactory $design
     * @param ResourceModel\App\CollectionFactory $appCollection
     * @param ResourceModel\Key\CollectionFactory $keyCollection
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Simi\Simiconnector\Model\ResourceModel\Siminotification $resource,
        \Simi\Simiconnector\Model\ResourceModel\Siminotification\Collection $resourceCollection,
        \Simi\Simiconnector\Helper\Website $websiteHelper

    )
    {

        $this->_websiteHelper = $websiteHelper;

        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection
        );
    }
    
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Simi\Simiconnector\Model\ResourceModel\Siminotification');
    }

    /**
     * @return array Website
     */
    public function toOptionStoreviewHash(){
        $storeViewCollection = \Magento\Framework\App\ObjectManager::getInstance()->get('\Magento\Store\Model\Store')->getCollection();
        $list = array();
        if(sizeof($storeViewCollection) > 0){
            foreach($storeViewCollection as $storeView){
                $list[$storeView->getId()] = $storeView->getName();
            }
        }
        return $list;
    }
    
    /**
     * @return array Website
     */
    public function toOptionWebsiteHash(){
        $website_collection = $this->_websiteHelper->getWebsiteCollection();
        $list = array();
        $list[0] = __('All');
        if(sizeof($website_collection) > 0){
            foreach($website_collection as $website){
                $list[$website->getId()] = $website->getName();
            }
        }
        return $list;
    }

    /**
     * @return array Website
     */
    public function toOptionCountryHash(){
        $country_collection = $this->_websiteHelper->getCountryCollection();
        $list = array();
        $list[] = __('All Countries');
        if(sizeof($country_collection) > 0){
            foreach($country_collection as $country){
                $list[$country->getId()] = $country->getName();
            }
        }
        return $list;
    }

    /**
     * @return array Siminotifications
     */
    public function toOptionDeviceHash(){
        $devices = array(
            '0' => __('All'),
            '1' => __('iOs'),
            '2' => __('Android'),
        );
        return $devices;
    }

    /**
     * @return array Type
     */
    public function toOptionTypeHash(){
        $platform = array(
            '1' => __('Product In-app'),
            '2' => __('Category In-app'),
            '3' => __('Website Page'),
        );
        return $platform;
    }

    /**
     * @return array Sandbox
     */
    public function toOptionSanboxHash(){
        $sandbox = array(
            '1' => __('Test App'),
            '2' => __('Live App'),
        );
        return $sandbox;
    }

    /**
     * @return array Popup
     */
    public function toOptionPopupHash(){
        $popup = array(
            '0' => __('No'),
            '1' => __('Yes'),
        );
        return $popup;
    }

}
