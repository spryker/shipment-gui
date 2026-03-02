<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ShipmentGui\Dependency\Facade;

use ArrayObject;
use Generated\Shared\Transfer\OrderTransfer;
use Generated\Shared\Transfer\SalesShipmentCollectionTransfer;
use Generated\Shared\Transfer\SalesShipmentCriteriaTransfer;
use Generated\Shared\Transfer\ShipmentCarrierRequestTransfer;
use Generated\Shared\Transfer\ShipmentCarrierTransfer;
use Generated\Shared\Transfer\ShipmentGroupResponseTransfer;
use Generated\Shared\Transfer\ShipmentGroupTransfer;
use Generated\Shared\Transfer\ShipmentMethodPluginCollectionTransfer;
use Generated\Shared\Transfer\ShipmentMethodTransfer;

class ShipmentGuiToShipmentFacadeBridge implements ShipmentGuiToShipmentFacadeInterface
{
    /**
     * @var \Spryker\Zed\Shipment\Business\ShipmentFacadeInterface
     */
    protected $shipmentFacade;

    /**
     * @param \Spryker\Zed\Shipment\Business\ShipmentFacadeInterface $shipmentFacade
     */
    public function __construct($shipmentFacade)
    {
        $this->shipmentFacade = $shipmentFacade;
    }

    /**
     * @return array<\Generated\Shared\Transfer\ShipmentMethodTransfer>
     */
    public function getMethods(): array
    {
        return $this->shipmentFacade->getMethods();
    }

    public function findMethodById(int $idShipmentMethod): ?ShipmentMethodTransfer
    {
        return $this->shipmentFacade->findMethodById($idShipmentMethod);
    }

    public function saveShipment(
        ShipmentGroupTransfer $shipmentGroupTransfer,
        OrderTransfer $orderTransfer
    ): ShipmentGroupResponseTransfer {
        return $this->shipmentFacade->saveShipment($shipmentGroupTransfer, $orderTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\ShipmentGroupTransfer $shipmentGroupTransfer
     * @param array<bool> $itemListUpdatedStatus
     *
     * @return \Generated\Shared\Transfer\ShipmentGroupTransfer
     */
    public function createShipmentGroupTransferWithListedItems(
        ShipmentGroupTransfer $shipmentGroupTransfer,
        array $itemListUpdatedStatus
    ): ShipmentGroupTransfer {
        return $this->shipmentFacade
            ->createShipmentGroupTransferWithListedItems($shipmentGroupTransfer, $itemListUpdatedStatus);
    }

    /**
     * @param int $idSalesOrder
     * @param int $idSalesShipment
     *
     * @return \ArrayObject<int, \Generated\Shared\Transfer\ItemTransfer>
     */
    public function findSalesOrderItemsIdsBySalesShipmentId(int $idSalesOrder, int $idSalesShipment): ArrayObject
    {
        return $this->shipmentFacade->findSalesOrderItemsIdsBySalesShipmentId($idSalesOrder, $idSalesShipment);
    }

    public function findShipmentCarrier(ShipmentCarrierRequestTransfer $shipmentCarrierRequestTransfer): ?ShipmentCarrierTransfer
    {
        return $this->shipmentFacade->findShipmentCarrier($shipmentCarrierRequestTransfer);
    }

    public function createCarrier(ShipmentCarrierTransfer $carrierTransfer): int
    {
        return $this->shipmentFacade->createCarrier($carrierTransfer);
    }

    public function findShipmentMethodByName(string $shipmentMethodName): ?ShipmentMethodTransfer
    {
        return $this->shipmentFacade->findShipmentMethodByName($shipmentMethodName);
    }

    public function findShipmentMethodByKey(string $shipmentMethodKey): ?ShipmentMethodTransfer
    {
        return $this->shipmentFacade->findShipmentMethodByKey($shipmentMethodKey);
    }

    public function createMethod(ShipmentMethodTransfer $methodTransfer): ?int
    {
        return $this->shipmentFacade->createMethod($methodTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\ShipmentMethodTransfer $methodTransfer
     *
     * @return int|bool
     */
    public function updateMethod(ShipmentMethodTransfer $methodTransfer)
    {
        return $this->shipmentFacade->updateMethod($methodTransfer);
    }

    public function deleteMethod(int $idMethod): bool
    {
        return $this->shipmentFacade->deleteMethod($idMethod);
    }

    public function getShipmentMethodPlugins(): ShipmentMethodPluginCollectionTransfer
    {
        return $this->shipmentFacade->getShipmentMethodPlugins();
    }

    /**
     * @return array<\Generated\Shared\Transfer\ShipmentCarrierTransfer>
     */
    public function getActiveShipmentCarriers(): array
    {
        return $this->shipmentFacade->getActiveShipmentCarriers();
    }

    public function getSalesShipmentCollection(
        SalesShipmentCriteriaTransfer $salesShipmentCriteriaTransfer
    ): SalesShipmentCollectionTransfer {
        return $this->shipmentFacade->getSalesShipmentCollection($salesShipmentCriteriaTransfer);
    }
}
