<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\CustomerCustomAttributes\Test\Fixture\Customer;

use Magento\CustomerCustomAttributes\Test\Fixture\CustomerCustomAttribute;
use Magento\Mtf\Fixture\DataSource;

/**
 * Source for attribute field.
 */
class CustomAttribute extends DataSource
{
    /**
     * @constructor
     * @param array $params
     * @param mixed $data
     */
    public function __construct(array $params, $data)
    {
        $this->params = $params;
        if (is_array($data)) {
            $this->data['value'] = isset($data['value']) ? $data['value'] : null;
            $this->data['code'] = $data['attribute'] instanceof CustomerCustomAttribute
                ? $data['attribute']->getAttributeCode() : null;
        } else {
            $this->data['value'] = $data;
        }
    }
}
