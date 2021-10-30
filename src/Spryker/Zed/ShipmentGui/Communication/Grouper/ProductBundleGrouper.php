<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ShipmentGui\Communication\Grouper;

use ArrayObject;
use Generated\Shared\Transfer\OrderTransfer;
use Generated\Shared\Transfer\ShipmentGroupTransfer;

class ProductBundleGrouper implements ProductBundleGrouperInterface
{
    /**
     * @param \ArrayObject<int, \Generated\Shared\Transfer\ShipmentGroupTransfer> $shipmentGroupTransfers
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return array<array<\Generated\Shared\Transfer\ProductBundleGroupTransfer>>
     */
    public function groupBundleItemsByShipmentGroupHash(ArrayObject $shipmentGroupTransfers, OrderTransfer $orderTransfer): array
    {
        $mappedGroupItems = [];
        $indexedProductBundleGroupTransfers = $this->indexProductBundleGroupItemsByItemGroupKey($orderTransfer->getItemGroups());

        foreach ($shipmentGroupTransfers as $shipmentGroupTransfer) {
            $shipmentGroupTransfer->requireHash();

            $mappedGroupItems[$shipmentGroupTransfer->getHash()] = $this->getGroupItemsForShipmentGroup($shipmentGroupTransfer, $indexedProductBundleGroupTransfers);
        }

        return $mappedGroupItems;
    }

    /**
     * @param \Generated\Shared\Transfer\ShipmentGroupTransfer $shipmentGroupTransfer
     * @param array<\Generated\Shared\Transfer\ProductBundleGroupTransfer> $indexedProductBundleGroupTransfers
     *
     * @return array<\Generated\Shared\Transfer\ProductBundleGroupTransfer>
     */
    protected function getGroupItemsForShipmentGroup(ShipmentGroupTransfer $shipmentGroupTransfer, array $indexedProductBundleGroupTransfers): array
    {
        $productBundleGroupTransfers = [];

        foreach ($shipmentGroupTransfer->getItems() as $itemTransfer) {
            $productBundleGroupTransfer = $indexedProductBundleGroupTransfers[$itemTransfer->getGroupKey()] ?? null;

            if (!$productBundleGroupTransfer || !$productBundleGroupTransfer->getIsBundle()) {
                continue;
            }

            $productBundleGroupTransfers[$productBundleGroupTransfer->getBundleItem()->getBundleItemIdentifier()] = $productBundleGroupTransfer;
        }

        return $productBundleGroupTransfers;
    }

    /**
     * @param \ArrayObject<int, \Generated\Shared\Transfer\ProductBundleGroupTransfer> $productBundleGroupTransfers
     *
     * @return array<\Generated\Shared\Transfer\ProductBundleGroupTransfer>
     */
    protected function indexProductBundleGroupItemsByItemGroupKey(ArrayObject $productBundleGroupTransfers): array
    {
        $indexedProductBundleGroupTransfers = [];

        foreach ($productBundleGroupTransfers as $productBundleGroupTransfer) {
            foreach ($productBundleGroupTransfer->getGroupItems() as $itemTransfer) {
                $indexedProductBundleGroupTransfers[$itemTransfer->getGroupKey()] = $productBundleGroupTransfer;
            }
        }

        return $indexedProductBundleGroupTransfers;
    }
}
