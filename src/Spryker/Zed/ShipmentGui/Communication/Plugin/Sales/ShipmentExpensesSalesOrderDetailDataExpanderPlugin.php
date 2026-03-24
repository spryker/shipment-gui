<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ShipmentGui\Communication\Plugin\Sales;

use Generated\Shared\Transfer\OrderTransfer;
use Spryker\Shared\ShipmentGui\ShipmentGuiConfig;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\SalesExtension\Dependency\Plugin\SalesOrderDetailDataExpanderPluginInterface;

/**
 * @method \Spryker\Zed\ShipmentGui\Communication\ShipmentGuiCommunicationFactory getFactory()
 * @method \Spryker\Zed\ShipmentGui\ShipmentGuiConfig getConfig()
 */
class ShipmentExpensesSalesOrderDetailDataExpanderPlugin extends AbstractPlugin implements SalesOrderDetailDataExpanderPluginInterface
{
    protected const string KEY_SHIPMENT_EXPENSE_TYPE = 'shipmentExpenseType';

    /**
     * {@inheritDoc}
     * - Expands order detail data with shipment expense type constant.
     * - Adds `shipmentExpenseType` for shipment expense identification in templates.
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
        $orderDetailData[static::KEY_SHIPMENT_EXPENSE_TYPE] = ShipmentGuiConfig::SHIPMENT_EXPENSE_TYPE;

        return $orderDetailData;
    }
}
