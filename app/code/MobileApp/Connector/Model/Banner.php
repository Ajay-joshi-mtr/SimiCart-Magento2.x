<?php

namespace MobileApp\Connector\Model;

/**
 * Connector Model
 *
 * @method \MobileApp\Connector\Model\Resource\Page _getResource()
 * @method \MobileApp\Connector\Model\Resource\Page getResource()
 */
class Banner extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('MobileApp\Connector\Model\ResourceModel\Banner');
    }

}
