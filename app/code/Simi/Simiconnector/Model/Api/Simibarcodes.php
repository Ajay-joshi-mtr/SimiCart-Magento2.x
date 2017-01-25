<?php
/**
 * Copyright © 2016 Simi. All rights reserved.
 */

namespace Simi\Simiconnector\Model\Api;

class Simibarcodes extends Apiabstract
{
    protected $_DEFAULT_ORDER = 'barcode_id';

    public function setBuilderQuery() {
        $data = $this->getData();
        if ($data['resourceid']) {
            $this->builderQuery = $this->_objectManager->get('Simi\Simiconnector\Model\Simibarcode')->getCollection()->addFieldToFilter('barcode_status', '1')->addFieldToFilter('barcode', $data['resourceid'])->setPageSize(1)->getFirstItem();
            if (!$this->builderQuery->getId())
                $this->builderQuery = $this->_objectManager->get('Simi\Simiconnector\Model\Simibarcode')->getCollection()->addFieldToFilter('barcode_status', '1')->addFieldToFilter('qrcode', $data['resourceid'])->setPageSize(1)->getFirstItem();
            if (!$this->builderQuery->getId())
                throw new \Exception(__('There is No Product Matchs your Code'), 4);
        } else {
            $this->builderQuery = $this->_objectManager->get('Simi\Simiconnector\Model\Simibarcode')->getCollection();
        }
    }
}
