<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Banner\Test\Fixture\Banner;

use Magento\CatalogRule\Test\Fixture\CatalogRule;
use Magento\Mtf\Fixture\FixtureFactory;
use Magento\Mtf\Fixture\DataSource;
use Magento\CustomerSegment\Test\Fixture\CustomerSegment;

/**
 * Prepare catalog rules.
 */
class CustomerSegmentIds extends DataSource
{
    /**
     * Return customer segment.
     *
     * @var CustomerSegment
     */
    protected $customerSegment = [];

    /**
     * @constructor
     * @param FixtureFactory $fixtureFactory
     * @param array $params
     * @param array $data
     */
    public function __construct(FixtureFactory $fixtureFactory, array $params, array $data = [])
    {
        $this->params = $params;
        if ($data['dataset'] && $data['dataset'] != "-") {
            $dataset = explode(',', $data['dataset']);
            foreach ($dataset as $customerSegment) {
                /** @var CustomerSegment $segment */
                $segment = $fixtureFactory->createByCode('customerSegment', ['dataset' => $customerSegment]);
                if (!$segment->getSegmentId()) {
                    $segment->persist();
                }
                $this->customerSegment[] = $segment;
                $this->data[] = $segment->getSegmentId();
            }
        } else {
            $this->data[] = null;
        }
    }

    /**
     * Return customer segment fixture.
     *
     * @return CustomerSegment
     */
    public function getCustomerSegments()
    {
        return $this->customerSegment;
    }
}
