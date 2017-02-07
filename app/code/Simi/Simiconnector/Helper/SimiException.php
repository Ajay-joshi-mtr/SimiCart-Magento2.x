<?php
/**
 * Localized Exception
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Simi\Simiconnector\Helper;

/**
 * @api
 */
class SimiException extends \Exception
{
    public function __construct($messageStr, $errorNum = null)
    {
        parent::__construct($messageStr, $errorNum, null);
    }
}
