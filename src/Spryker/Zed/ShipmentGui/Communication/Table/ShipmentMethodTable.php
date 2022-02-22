<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ShipmentGui\Communication\Table;

use Orm\Zed\Shipment\Persistence\Map\SpyShipmentCarrierTableMap;
use Orm\Zed\Shipment\Persistence\Map\SpyShipmentMethodTableMap;
use Orm\Zed\Shipment\Persistence\SpyShipmentMethod;
use Orm\Zed\Shipment\Persistence\SpyShipmentMethodQuery;
use Orm\Zed\Store\Persistence\Map\SpyStoreTableMap;
use Spryker\Service\UtilText\Model\Url\Url;
use Spryker\Zed\Gui\Communication\Table\AbstractTable;
use Spryker\Zed\Gui\Communication\Table\TableConfiguration;

class ShipmentMethodTable extends AbstractTable
{
    /**
     * @var string
     */
    protected const COL_ACTIONS = 'Actions';

    /**
     * @var string
     */
    protected const PARAM_ID_SHIPMENT_METHOD = 'id-shipment-method';

    /**
     * @var string
     */
    protected const URL_SHIPMENT_METHOD_VIEW = '/shipment-gui/view-shipment-method/index';

    /**
     * @var string
     */
    protected const URL_SHIPMENT_METHOD_EDIT = '/shipment-gui/update-shipment-method/index';

    /**
     * @var string
     */
    protected const URL_SHIPMENT_METHOD_DELETE = '/shipment-gui/delete-shipment-method/index';

    /**
     * @var string
     */
    protected const BUTTON_VIEW = 'View';

    /**
     * @var string
     */
    protected const BUTTON_EDIT = 'Edit';

    /**
     * @var string
     */
    protected const BUTTON_DELETE = 'Delete';

    /**
     * @var string
     */
    protected const HEADER_DELIVERY_METHOD_KEY = 'Delivery Method Key';

    /**
     * @var string
     */
    protected const HEADER_CARRIER_COMPANY = 'Carrier Company';

    /**
     * @var string
     */
    protected const HEADER_METHOD_NAME = 'Method Name';

    /**
     * @var string
     */
    protected const HEADER_STATUS = 'Status';

    /**
     * @var string
     */
    protected const HEADER_AVAILABLE_IN_STORE = 'Available in Store';

    /**
     * @var string
     */
    protected const HEADER_ACTIONS = 'Actions';

    /**
     * @var \Orm\Zed\Shipment\Persistence\SpyShipmentMethodQuery
     */
    protected $shipmentMethodQuery;

    /**
     * @param \Orm\Zed\Shipment\Persistence\SpyShipmentMethodQuery $shipmentMethodQuery
     */
    public function __construct(SpyShipmentMethodQuery $shipmentMethodQuery)
    {
        $this->shipmentMethodQuery = $shipmentMethodQuery;
    }

    /**
     * @param \Spryker\Zed\Gui\Communication\Table\TableConfiguration $config
     *
     * @return array
     */
    protected function prepareData(TableConfiguration $config): array
    {
        /** @var array<\Orm\Zed\Shipment\Persistence\SpyShipmentMethod> $shipmentMethodEntities */
        $shipmentMethodEntities = $this->runQuery($this->prepareQuery(), $config, true);

        $shipmentMethodRows = [];
        foreach ($shipmentMethodEntities as $shipmentMethodEntity) {
            $shipmentMethodRows[] = $this->mapShipmentMethodRow($shipmentMethodEntity);
        }

        return $shipmentMethodRows;
    }

    /**
     * @return \Orm\Zed\Shipment\Persistence\SpyShipmentMethodQuery
     */
    protected function prepareQuery(): SpyShipmentMethodQuery
    {
        return $this->shipmentMethodQuery
            ->leftJoinWithShipmentCarrier();
    }

    /**
     * @param \Orm\Zed\Shipment\Persistence\SpyShipmentMethod $shipmentMethodEntity
     *
     * @return array
     */
    protected function mapShipmentMethodRow(SpyShipmentMethod $shipmentMethodEntity): array
    {
        return [
            SpyShipmentMethodTableMap::COL_SHIPMENT_METHOD_KEY => $shipmentMethodEntity->getShipmentMethodKey(),
            SpyShipmentCarrierTableMap::COL_NAME => $shipmentMethodEntity->getShipmentCarrier()->getName(),
            SpyShipmentMethodTableMap::COL_NAME => $shipmentMethodEntity->getName(),
            SpyShipmentMethodTableMap::COL_IS_ACTIVE => $this->generateIsActiveLabel($shipmentMethodEntity),
            SpyStoreTableMap::COL_NAME => $this->getStoreNames($shipmentMethodEntity),
            static::COL_ACTIONS => $this->buildLinks($shipmentMethodEntity),
        ];
    }

    /**
     * @param \Orm\Zed\Shipment\Persistence\SpyShipmentMethod $shipmentMethodEntity
     *
     * @return string
     */
    protected function generateIsActiveLabel(SpyShipmentMethod $shipmentMethodEntity): string
    {
        return $shipmentMethodEntity->isActive() ? $this->generateLabel('Active', 'label-primary')
            : $this->generateLabel('Inactive', 'label-light');
    }

    /**
     * @param \Orm\Zed\Shipment\Persistence\SpyShipmentMethod $shipmentMethodEntity
     *
     * @return string
     */
    protected function buildLinks(SpyShipmentMethod $shipmentMethodEntity): string
    {
        return implode(' ', [
            $this->generateShipmentMethodViewButton($shipmentMethodEntity),
            $this->generateShipmentMethodEditButton($shipmentMethodEntity),
            $this->generateShipmentMethodDeleteButton($shipmentMethodEntity),
        ]);
    }

    /**
     * @param \Orm\Zed\Shipment\Persistence\SpyShipmentMethod $shipmentMethodEntity
     *
     * @return string
     */
    protected function generateShipmentMethodViewButton(SpyShipmentMethod $shipmentMethodEntity): string
    {
        return $this->generateViewButton(
            Url::generate(static::URL_SHIPMENT_METHOD_VIEW, [
                static::PARAM_ID_SHIPMENT_METHOD => $shipmentMethodEntity->getIdShipmentMethod(),
            ]),
            static::BUTTON_VIEW,
        );
    }

    /**
     * @param \Orm\Zed\Shipment\Persistence\SpyShipmentMethod $shipmentMethodEntity
     *
     * @return string
     */
    protected function generateShipmentMethodEditButton(SpyShipmentMethod $shipmentMethodEntity): string
    {
        return $this->generateEditButton(
            Url::generate(static::URL_SHIPMENT_METHOD_EDIT, [
                static::PARAM_ID_SHIPMENT_METHOD => $shipmentMethodEntity->getIdShipmentMethod(),
            ]),
            static::BUTTON_EDIT,
        );
    }

    /**
     * @param \Orm\Zed\Shipment\Persistence\SpyShipmentMethod $shipmentMethodEntity
     *
     * @return string
     */
    protected function generateShipmentMethodDeleteButton(SpyShipmentMethod $shipmentMethodEntity): string
    {
        return $this->generateRemoveButton(
            Url::generate(static::URL_SHIPMENT_METHOD_DELETE, [
                static::PARAM_ID_SHIPMENT_METHOD => $shipmentMethodEntity->getIdShipmentMethod(),
            ]),
            static::BUTTON_DELETE,
        );
    }

    /**
     * @param \Orm\Zed\Shipment\Persistence\SpyShipmentMethod $shipmentMethodEntity
     *
     * @return string
     */
    protected function getStoreNames(SpyShipmentMethod $shipmentMethodEntity): string
    {
        $storeNames = [];
        foreach ($shipmentMethodEntity->getShipmentMethodStores() as $shipmentMethodStore) {
            $storeNames[] = $this->generateLabel($shipmentMethodStore->getStore()->getName(), 'label-primary');
        }

        return implode(' ', $storeNames);
    }

    /**
     * @param \Spryker\Zed\Gui\Communication\Table\TableConfiguration $config
     *
     * @return \Spryker\Zed\Gui\Communication\Table\TableConfiguration
     */
    protected function configure(TableConfiguration $config): TableConfiguration
    {
        $config = $this->configureHeader($config);
        $config = $this->configureSortableColumns($config);
        $config = $this->configureSearchableColumns($config);
        $config = $this->setRawColumns($config);

        return $config;
    }

    /**
     * @param \Spryker\Zed\Gui\Communication\Table\TableConfiguration $tableConfiguration
     *
     * @return \Spryker\Zed\Gui\Communication\Table\TableConfiguration
     */
    protected function configureHeader(TableConfiguration $tableConfiguration): TableConfiguration
    {
        $tableConfiguration->setHeader([
            SpyShipmentMethodTableMap::COL_SHIPMENT_METHOD_KEY => static::HEADER_DELIVERY_METHOD_KEY,
            SpyShipmentCarrierTableMap::COL_NAME => static::HEADER_CARRIER_COMPANY,
            SpyShipmentMethodTableMap::COL_NAME => static::HEADER_METHOD_NAME,
            SpyShipmentMethodTableMap::COL_IS_ACTIVE => static::HEADER_STATUS,
            SpyStoreTableMap::COL_NAME => static::HEADER_AVAILABLE_IN_STORE,
            static::COL_ACTIONS => static::HEADER_ACTIONS,
        ]);

        return $tableConfiguration;
    }

    /**
     * @param \Spryker\Zed\Gui\Communication\Table\TableConfiguration $tableConfiguration
     *
     * @return \Spryker\Zed\Gui\Communication\Table\TableConfiguration
     */
    protected function configureSortableColumns(TableConfiguration $tableConfiguration): TableConfiguration
    {
        $tableConfiguration->setSortable([
            SpyShipmentMethodTableMap::COL_SHIPMENT_METHOD_KEY,
            SpyShipmentCarrierTableMap::COL_NAME,
            SpyShipmentMethodTableMap::COL_NAME,
            SpyShipmentMethodTableMap::COL_IS_ACTIVE,
        ]);

        return $tableConfiguration;
    }

    /**
     * @param \Spryker\Zed\Gui\Communication\Table\TableConfiguration $tableConfiguration
     *
     * @return \Spryker\Zed\Gui\Communication\Table\TableConfiguration
     */
    protected function configureSearchableColumns(TableConfiguration $tableConfiguration): TableConfiguration
    {
        $tableConfiguration->setSearchable([
            SpyShipmentCarrierTableMap::COL_NAME,
            SpyShipmentMethodTableMap::COL_NAME,
        ]);

        return $tableConfiguration;
    }

    /**
     * @param \Spryker\Zed\Gui\Communication\Table\TableConfiguration $tableConfiguration
     *
     * @return \Spryker\Zed\Gui\Communication\Table\TableConfiguration
     */
    protected function setRawColumns(TableConfiguration $tableConfiguration): TableConfiguration
    {
        $tableConfiguration->setRawColumns([
            SpyShipmentMethodTableMap::COL_IS_ACTIVE,
            SpyStoreTableMap::COL_NAME,
            static::COL_ACTIONS,
        ]);

        return $tableConfiguration;
    }
}
