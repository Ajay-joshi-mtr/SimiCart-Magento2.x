<?php

/**
 * Connector Resource Collection
 */
namespace Simi\Simiconnector\Model\ResourceModel\Simibarcode;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Simi\Simiconnector\Model\Simibarcode', 'Simi\Simiconnector\Model\ResourceModel\Simibarcode');
    }
}
