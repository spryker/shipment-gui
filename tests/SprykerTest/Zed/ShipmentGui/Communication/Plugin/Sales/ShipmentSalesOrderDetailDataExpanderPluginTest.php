<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\ShipmentGui\Communication\Plugin\Sales;

use ArrayObject;
use Codeception\Test\Unit;
use Generated\Shared\Transfer\OrderTransfer;
use Generated\Shared\Transfer\ShipmentGroupTransfer;
use Generated\Shared\Transfer\ShipmentTransfer;
use Spryker\Zed\ShipmentGui\Communication\Plugin\Sales\ShipmentSalesOrderDetailDataExpanderPlugin;
use SprykerTest\Zed\ShipmentGui\ShipmentGuiCommunicationTester;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group ShipmentGui
 * @group Communication
 * @group Plugin
 * @group Sales
 * @group ShipmentSalesOrderDetailDataExpanderPluginTest
 * Add your own group annotations below this line
 */
class ShipmentSalesOrderDetailDataExpanderPluginTest extends Unit
{
    protected const string DEFAULT_OMS_PROCESS_NAME = 'Test01';

    protected ShipmentGuiCommunicationTester $tester;

    public function getSalesOrderDetailDataExpanderPlugin(): ShipmentSalesOrderDetailDataExpanderPlugin
    {
        return new ShipmentSalesOrderDetailDataExpanderPlugin();
    }

    public function testExpandGroupsOrderItemsByShipment(): void
    {
        // Arrange
        $this->tester->configureTestStateMachine([static::DEFAULT_OMS_PROCESS_NAME]);
        $shipmentMethodTransfer = $this->tester->haveShipmentMethod();
        $saveOrderTransfer = $this->tester->haveOrder([], static::DEFAULT_OMS_PROCESS_NAME);

        $shipmentTransfer = (new ShipmentTransfer())->setMethod($shipmentMethodTransfer);
        $items = $saveOrderTransfer->getOrderItems();

        foreach ($items as $item) {
            $item->setShipment($shipmentTransfer);
        }

        $plugin = $this->getSalesOrderDetailDataExpanderPlugin();
        $orderTransfer = (new OrderTransfer())->fromArray($saveOrderTransfer->toArray(), true)->setItems($items);

        // Act
        $result = $plugin->expand($orderTransfer, []);

        // Assert
        $this->assertInstanceOf(ArrayObject::class, $result['groupedOrderItemsByShipment']);
        $this->assertGreaterThan(0, $result['groupedOrderItemsByShipment']->count());
        $this->assertContainsOnlyInstancesOf(ShipmentGroupTransfer::class, $result['groupedOrderItemsByShipment']);
        $this->assertNotEmpty($result['itemGroups']);
    }

    public function testExpandItemGroupsAreIndexedByShipmentGroupHash(): void
    {
        // Arrange
        $this->tester->configureTestStateMachine([static::DEFAULT_OMS_PROCESS_NAME]);
        $shipmentMethodTransfer = $this->tester->haveShipmentMethod();
        $saveOrderTransfer = $this->tester->haveOrder([], static::DEFAULT_OMS_PROCESS_NAME);

        $shipmentTransfer = (new ShipmentTransfer())->setMethod($shipmentMethodTransfer);
        $items = $saveOrderTransfer->getOrderItems();

        foreach ($items as $item) {
            $item->setShipment($shipmentTransfer);
        }

        $plugin = $this->getSalesOrderDetailDataExpanderPlugin();
        $orderTransfer = (new OrderTransfer())->fromArray($saveOrderTransfer->toArray(), true)->setItems($items);

        // Act
        $result = $plugin->expand($orderTransfer, []);

        // Assert
        /** @var \ArrayObject<int, \Generated\Shared\Transfer\ShipmentGroupTransfer> $groupedOrderItemsByShipment */
        $groupedOrderItemsByShipment = $result['groupedOrderItemsByShipment'];
        $expectedHashes = array_map(
            fn (ShipmentGroupTransfer $group): string => $group->getHash(),
            iterator_to_array($groupedOrderItemsByShipment),
        );
        $actualHashes = array_keys($result['itemGroups']);
        sort($expectedHashes);
        sort($actualHashes);

        $this->assertSame($expectedHashes, $actualHashes);
    }

    public function testExpandAddsTemplatesAsArray(): void
    {
        // Arrange
        $this->tester->configureTestStateMachine([static::DEFAULT_OMS_PROCESS_NAME]);
        $shipmentMethodTransfer = $this->tester->haveShipmentMethod();
        $saveOrderTransfer = $this->tester->haveOrder([], static::DEFAULT_OMS_PROCESS_NAME);

        $shipmentTransfer = (new ShipmentTransfer())->setMethod($shipmentMethodTransfer);
        $items = $saveOrderTransfer->getOrderItems();

        foreach ($items as $item) {
            $item->setShipment($shipmentTransfer);
        }

        $plugin = $this->getSalesOrderDetailDataExpanderPlugin();
        $orderTransfer = (new OrderTransfer())->fromArray($saveOrderTransfer->toArray(), true)->setItems($items);

        // Act
        $result = $plugin->expand($orderTransfer, []);

        // Assert
        $this->assertIsArray($result['templates']);
    }

    public function testExpandPreservesExistingData(): void
    {
        // Arrange
        $this->tester->configureTestStateMachine([static::DEFAULT_OMS_PROCESS_NAME]);
        $shipmentMethodTransfer = $this->tester->haveShipmentMethod();
        $saveOrderTransfer = $this->tester->haveOrder([], static::DEFAULT_OMS_PROCESS_NAME);

        $shipmentTransfer = (new ShipmentTransfer())->setMethod($shipmentMethodTransfer);
        $items = $saveOrderTransfer->getOrderItems();

        foreach ($items as $item) {
            $item->setShipment($shipmentTransfer);
        }

        $plugin = $this->getSalesOrderDetailDataExpanderPlugin();
        $orderTransfer = (new OrderTransfer())->fromArray($saveOrderTransfer->toArray(), true)->setItems($items);
        $existingData = ['someKey' => 'someValue'];

        // Act
        $result = $plugin->expand($orderTransfer, $existingData);

        // Assert
        $this->assertSame('someValue', $result['someKey']);
        $this->assertInstanceOf(ArrayObject::class, $result['groupedOrderItemsByShipment']);
    }
}
