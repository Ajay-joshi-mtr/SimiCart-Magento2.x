<?php

namespace Simi\Simiconnector\Model;

/**
 * Connector Model
 *
 * @method \Simi\Simiconnector\Model\Resource\Page _getResource()
 * @method \Simi\Simiconnector\Model\Resource\Page getResource()
 */
class History extends \Magento\Framework\Model\AbstractModel
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
        \Simi\Simiconnector\Model\ResourceModel\History $resource,
        \Simi\Simiconnector\Model\ResourceModel\History\Collection $resourceCollection,
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
        $this->_init('Simi\Simiconnector\Model\ResourceModel\History');
    }

    /**
     * @return array Devices
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
     * @return array Types
     */
    public function toOptionTypeHash(){
        $devices = array(
            '0' => __('Custom'),
            '1' => __('Price Updates'),
            '2' => __('New Product'),
            '3' => __('Order Purchase'),
        );
        return $devices;
    }

    /**
     * @return array Status
     */
    public function toOptionStatusHash(){
        $devices = array(
            '0' => __('Unsuccessfully'),
            '1' => __('Successfully'),
        );
        return $devices;
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

}
