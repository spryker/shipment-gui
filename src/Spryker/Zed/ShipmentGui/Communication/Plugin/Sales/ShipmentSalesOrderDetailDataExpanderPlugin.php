<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ShipmentGui\Communication\Plugin\Sales;

use Generated\Shared\Transfer\OrderTransfer;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\SalesExtension\Dependency\Plugin\SalesOrderDetailDataExpanderPluginInterface;

/**
 * @method \Spryker\Zed\ShipmentGui\Communication\ShipmentGuiCommunicationFactory getFactory()
 * @method \Spryker\Zed\ShipmentGui\ShipmentGuiConfig getConfig()
 */
class ShipmentSalesOrderDetailDataExpanderPlugin extends AbstractPlugin implements SalesOrderDetailDataExpanderPluginInterface
{
    /**
     * {@inheritDoc}
     * - Expands order detail data with shipment-related information.
     * - Adds `groupedOrderItemsByShipment` - order items grouped by shipment.
     * - Adds `itemGroups` - product bundle items grouped by shipment hash.
     * - Adds `templates` - template paths for rendering order items.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @param array<string, mixed> $orderDetailData
     *
     * @return array<string, mixed>
     */
    public function expand(OrderTransfer $orderTransfer, array $orderDetailData): array
    {
        return $this->getFactory()->createShipmentOrderDetailDataExpander()->expand($orderTransfer, $orderDetailData);
    }
}
