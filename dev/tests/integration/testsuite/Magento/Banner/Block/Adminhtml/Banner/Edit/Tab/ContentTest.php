<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Banner\Block\Adminhtml\Banner\Edit\Tab;

/**
 * @magentoAppArea adminhtml
 */
class ContentTest extends \PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $this->assertInstanceOf(
            \Magento\Banner\Block\Adminhtml\Banner\Edit\Tab\Content::class,
            \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
                \Magento\Framework\View\LayoutInterface::class
            )->createBlock(
                \Magento\Banner\Block\Adminhtml\Banner\Edit\Tab\Content::class
            )
        );
    }
}
