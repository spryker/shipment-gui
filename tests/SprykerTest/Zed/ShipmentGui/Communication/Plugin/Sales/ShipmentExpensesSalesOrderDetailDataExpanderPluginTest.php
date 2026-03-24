<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\ShipmentGui\Communication\Plugin\Sales;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\OrderTransfer;
use Spryker\Shared\ShipmentGui\ShipmentGuiConfig;
use Spryker\Zed\ShipmentGui\Communication\Plugin\Sales\ShipmentExpensesSalesOrderDetailDataExpanderPlugin;
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
 * @group ShipmentExpensesSalesOrderDetailDataExpanderPluginTest
 * Add your own group annotations below this line
 */
class ShipmentExpensesSalesOrderDetailDataExpanderPluginTest extends Unit
{
    protected ShipmentGuiCommunicationTester $tester;

    public function testExpandAddsShipmentExpenseType(): void
    {
        // Arrange
        $plugin = $this->getSalesOrderDetailDataExpanderPlugin();
        $orderTransfer = new OrderTransfer();

        // Act
        $result = $plugin->expand($orderTransfer, []);

        // Assert
        $this->assertArrayHasKey('shipmentExpenseType', $result);
        $this->assertSame(ShipmentGuiConfig::SHIPMENT_EXPENSE_TYPE, $result['shipmentExpenseType']);
    }

    public function testExpandPreservesExistingData(): void
    {
        // Arrange
        $plugin = $this->getSalesOrderDetailDataExpanderPlugin();
        $orderTransfer = new OrderTransfer();
        $existingData = ['someKey' => 'someValue'];

        // Act
        $result = $plugin->expand($orderTransfer, $existingData);

        // Assert
        $this->assertArrayHasKey('someKey', $result);
        $this->assertArrayHasKey('shipmentExpenseType', $result);
    }

    public function getSalesOrderDetailDataExpanderPlugin(): ShipmentExpensesSalesOrderDetailDataExpanderPlugin
    {
        return new ShipmentExpensesSalesOrderDetailDataExpanderPlugin();
    }
}
