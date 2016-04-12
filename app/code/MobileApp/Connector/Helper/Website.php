<?php

/**
 * Connector data helper
 */
namespace MobileApp\Connector\Helper;

class Website extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \MobileApp\Connector\Model\Connector
     */
    protected $_websiteFactory;

    /**
     * @var \MobileApp\Connector\Model\Connector
     */
    protected $_websiteRepositoryFactory;

    /**
     * @var https|http
     */
    protected $_request;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\ResourceModel\Website\CollectionFactory $websiteFactory,
        \Magento\Store\Model\WebsiteRepositoryFactory $websiteRepositoryFactory,
        \Magento\Framework\App\Request\Http $request
    )
    {
        $this->_request = $request;
        $this->_websiteFactory = $websiteFactory;
        $this->_websiteRepositoryFactory = $websiteRepositoryFactory;

        parent::__construct($context);
    }

    /**
     * @return int|mixed
     */
    public function getWebsiteIdFromUrl()
    {
        $website_id = $this->_request->getParam('website_id');
        if ($website_id != null)
            return $website_id;
        else
            return $this->getDefaultWebsite()->getId();
    }

    public function getDefaultWebsite()
    {
        $website = $this->_websiteRepositoryFactory->create()->getDefault();
        return $website;
    }
}
