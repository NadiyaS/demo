<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Signifyd\Observer;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Event;
use Magento\Framework\Event\Observer;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Signifyd\Api\CaseCreationServiceInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Psr\Log\LoggerInterface;

class PlaceOrderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CaseCreationServiceInterface|MockObject
     */
    private $creationService;

    /**
     * @var LoggerInterface|MockObject
     */
    private $logger;

    /**
     * @var PlaceOrder
     */
    private $placeOrder;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $this->objectManager = Bootstrap::getObjectManager();

        $this->creationService = $this->getMockBuilder(CaseCreationServiceInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['createForOrder'])
            ->getMock();

        $this->logger = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->placeOrder = $this->objectManager->create(PlaceOrder::class, [
            'caseCreationService' => $this->creationService,
            'logger' => $this->logger
        ]);
    }

    /**
     * Checks a case when order placed with offline payment method.
     *
     * @covers \Magento\Signifyd\Observer\PlaceOrder::execute
     * @magentoConfigFixture current_store fraud_protection/signifyd/active 1
     * @magentoDataFixture Magento/Signifyd/_files/order_with_customer_and_two_simple_products.php
     */
    public function testExecuteWithOfflinePayment()
    {
        $order = $this->getOrder('100000005');
        $this->creationService->expects(self::never())
            ->method('createForOrder');

        $event = $this->objectManager->create(
            Event::class,
            [
                'data' => ['order' => $order]
            ]
        );

        /** @var Observer $observer */
        $observer = $this->objectManager->get(Observer::class);
        $observer->setEvent($event);

        $this->placeOrder->execute($observer);
    }

    /**
     * Checks a test case when order placed with online payment method.
     *
     * @covers \Magento\Signifyd\Observer\PlaceOrder::execute
     * @magentoConfigFixture current_store fraud_protection/signifyd/active 1
     * @magentoDataFixture Magento/Signifyd/_files/order_with_customer_and_two_simple_products.php
     */
    public function testExecute()
    {
        $order = $this->getOrder('100000001');

        $this->creationService->expects(self::once())
            ->method('createForOrder')
            ->with(self::equalTo($order->getEntityId()));

        $event = $this->objectManager->create(
            Event::class,
            [
                'data' => ['order' => $order]
            ]
        );

        /** @var Observer $observer */
        $observer = $this->objectManager->get(Observer::class);
        $observer->setEvent($event);

        $this->placeOrder->execute($observer);
    }

    /**
     * Checks a test case when observer event contains two orders:
     * one order with offline payment and one order with online payment.
     *
     * @covers \Magento\Signifyd\Observer\PlaceOrder::execute
     * @magentoConfigFixture current_store fraud_protection/signifyd/active 1
     * @magentoDataFixture Magento/Signifyd/_files/order_with_customer_and_two_simple_products.php
     */
    public function testExecuteWithMultipleOrders()
    {
        $orderWithOnlinePayment = $this->getOrder('100000001');
        $orderWithOfflinePayment = $this->getOrder('100000005');

        // this service mock should be called only once for the order with online payment method.
        $this->creationService->expects(self::once())
            ->method('createForOrder')
            ->with(self::equalTo($orderWithOnlinePayment->getEntityId()));

        $event = $this->objectManager->create(
            Event::class,
            [
                'data' => ['orders' => [$orderWithOfflinePayment, $orderWithOnlinePayment]]
            ]
        );

        /** @var Observer $observer */
        $observer = $this->objectManager->get(Observer::class);
        $observer->setEvent($event);

        $this->placeOrder->execute($observer);
    }

    /**
     * Gets stored order.
     *
     * @param string $incrementId
     * @return OrderInterface
     */
    private function getOrder($incrementId)
    {
        /** @var FilterBuilder $filterBuilder */
        $filterBuilder = $this->objectManager->get(FilterBuilder::class);
        $filters = [
            $filterBuilder->setField(OrderInterface::INCREMENT_ID)
                ->setValue($incrementId)
                ->create()
        ];

        /** @var SearchCriteriaBuilder $searchCriteriaBuilder */
        $searchCriteriaBuilder = $this->objectManager->get(SearchCriteriaBuilder::class);
        $searchCriteria = $searchCriteriaBuilder->addFilters($filters)
            ->create();

        $orderRepository = $this->objectManager->get(OrderRepositoryInterface::class);
        $orders = $orderRepository->getList($searchCriteria)
            ->getItems();

        $order = array_pop($orders);

        return $order;
    }
}
