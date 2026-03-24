<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ShipmentGui\Communication\Expander;

use Generated\Shared\Transfer\OrderTransfer;
use Spryker\Zed\ShipmentGui\Communication\Grouper\ProductBundleGrouperInterface;
use Spryker\Zed\ShipmentGui\Communication\Provider\ShipmentOrderItemTemplateProviderInterface;
use Spryker\Zed\ShipmentGui\Dependency\Service\ShipmentGuiToShipmentServiceInterface;

class ShipmentOrderDetailDataExpander implements ShipmentOrderDetailDataExpanderInterface
{
    protected const string KEY_GROUPED_ORDER_ITEMS_BY_SHIPMENT = 'groupedOrderItemsByShipment';

    protected const string KEY_ITEM_GROUPS = 'itemGroups';

    protected const string KEY_TEMPLATES = 'templates';

    public function __construct(
        protected readonly ShipmentGuiToShipmentServiceInterface $shipmentService,
        protected readonly ProductBundleGrouperInterface $productBundleGrouper,
        protected readonly ShipmentOrderItemTemplateProviderInterface $shipmentOrderItemTemplateProvider,
    ) {
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @param array<string, mixed> $orderDetailData
     *
     * @return array<string, mixed>
     */
    public function expand(OrderTransfer $orderTransfer, array $orderDetailData): array
    {
        $shipmentGroupsCollection = $this->shipmentService->groupItemsByShipment($orderTransfer->getItems());

        return array_merge($orderDetailData, [
            static::KEY_GROUPED_ORDER_ITEMS_BY_SHIPMENT => $shipmentGroupsCollection,
            static::KEY_ITEM_GROUPS => $this->productBundleGrouper->groupBundleItemsByShipmentGroupHash($shipmentGroupsCollection, $orderTransfer),
            static::KEY_TEMPLATES => $this->shipmentOrderItemTemplateProvider->provide($orderTransfer->getItems()),
        ]);
    }
}
