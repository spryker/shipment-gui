<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ShipmentGui\Communication\Form\DataProvider;

use ArrayObject;
use Generated\Shared\Transfer\AddressTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use Generated\Shared\Transfer\ShipmentFormTransfer;
use Generated\Shared\Transfer\ShipmentTransfer;
use Orm\Zed\Customer\Persistence\Map\SpyCustomerTableMap;
use Spryker\Zed\ShipmentGui\Communication\Form\Address\AddressForm;
use Spryker\Zed\ShipmentGui\Communication\Form\Item\ItemForm;
use Spryker\Zed\ShipmentGui\Communication\Form\ShipmentFormCreate;
use Spryker\Zed\ShipmentGui\Dependency\Facade\ShipmentGuiToCustomerFacadeInterface;
use Spryker\Zed\ShipmentGui\Dependency\Facade\ShipmentGuiToSalesFacadeInterface;
use Spryker\Zed\ShipmentGui\Dependency\Facade\ShipmentGuiToShipmentFacadeInterface;

class ShipmentFormDefaultDataProvider
{
    public const OPTION_DATA_CLASS = 'data_class';

    protected const ADDRESS_LABEL_PATTERN = '%s %s %s, %s %s, %s %s';
    protected const SHIPMENT_METHODS_OPTIONS_NAMES_PATTERN = '%s - %s';

    /**
     * @var \Spryker\Zed\ShipmentGui\Dependency\Facade\ShipmentGuiToSalesFacadeInterface
     */
    protected $salesFacade;

    /**
     * @var \Spryker\Zed\ShipmentGui\Dependency\Facade\ShipmentGuiToCustomerFacadeInterface
     */
    protected $customerFacade;

    /**
     * @var \Spryker\Zed\ShipmentGui\Dependency\Facade\ShipmentGuiToShipmentFacadeInterface
     */
    protected $shipmentFacade;

    /**
     * @param \Spryker\Zed\ShipmentGui\Dependency\Facade\ShipmentGuiToSalesFacadeInterface $salesFacade
     * @param \Spryker\Zed\ShipmentGui\Dependency\Facade\ShipmentGuiToCustomerFacadeInterface $customerFacade
     * @param \Spryker\Zed\ShipmentGui\Dependency\Facade\ShipmentGuiToShipmentFacadeInterface $shipmentFacade
     */
    public function __construct(
        ShipmentGuiToSalesFacadeInterface $salesFacade,
        ShipmentGuiToCustomerFacadeInterface $customerFacade,
        ShipmentGuiToShipmentFacadeInterface $shipmentFacade
    ) {
        $this->salesFacade = $salesFacade;
        $this->customerFacade = $customerFacade;
        $this->shipmentFacade = $shipmentFacade;
    }

    /**
     * @param int $idSalesOrder
     * @param int|null $idSalesShipment
     *
     * @return array
     */
    public function getDefaultFormFields(int $idSalesOrder, ?int $idSalesShipment = null): array
    {
        $defaultShipmentFormCreateFields = [
            ShipmentFormCreate::FIELD_ID_SALES_SHIPMENT => $idSalesShipment,
            ShipmentFormCreate::FIELD_ID_SALES_ORDER => $idSalesOrder,
            ShipmentFormCreate::FIELD_ID_CUSTOMER_ADDRESS => $this->getIdCustomerAddress($idSalesOrder, $idSalesShipment),
            ShipmentFormCreate::FIELD_ID_SHIPMENT_METHOD => null,
            ShipmentFormCreate::FIELD_REQUESTED_DELIVERY_DATE => null,
            ShipmentFormCreate::FORM_SHIPPING_ADDRESS => $this->getAddressDefaultFields(),
        ];

        return array_merge($defaultShipmentFormCreateFields, $this->getItemsDefaultFields($idSalesOrder));
    }

    /**
     * @param int $idSalesOrder
     * @param int|null $idSalesShipment
     *
     * @return int|null
     */
    protected function getIdCustomerAddress(int $idSalesOrder, ?int $idSalesShipment): ?int
    {
        if ($idSalesShipment === null) {
            return null;
        }

        $addressTransfer = $this->hydrateAddressTransfer(
            $this->salesFacade->findOrderByIdSalesOrder($idSalesOrder),
            $this->findShipmentById($idSalesShipment)
        );

        $customerAddressTransfer = $this->customerFacade->findCustomerAddressByAddressData($addressTransfer);

        if ($customerAddressTransfer === null) {
            return null;
        }

        return $customerAddressTransfer->getIdCustomerAddress();
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer|null $orderTransfer
     * @param \Generated\Shared\Transfer\ShipmentTransfer|null $shipmentTransfer
     *
     * @return \Generated\Shared\Transfer\AddressTransfer
     */
    protected function hydrateAddressTransfer(?OrderTransfer $orderTransfer, ?ShipmentTransfer $shipmentTransfer): AddressTransfer
    {
        $addressTransfer = new AddressTransfer();

        if ($shipmentTransfer !== null) {
            $addressTransfer->fromArray($shipmentTransfer->getShippingAddress()->toArray(), true);
        }

        $addressTransfer->setFkCustomer($orderTransfer->getFkCustomer());

        return $addressTransfer;
    }

    /**
     * @param int $idSalesOrder
     * @param int|null $idSalesShipment
     *
     * @return array
     */
    public function getOptions(int $idSalesOrder, ?int $idSalesShipment = null): array
    {
        $options = [
            static::OPTION_DATA_CLASS => ShipmentFormTransfer::class,
            ShipmentFormCreate::OPTION_SHIPMENT_ADDRESS_CHOICES => $this->getShippingAddressesOptions($idSalesOrder),
            ShipmentFormCreate::OPTION_SHIPMENT_METHOD_CHOICES => $this->getShippingMethodsOptions(),
            ShipmentFormCreate::FIELD_SHIPMENT_SELECTED_ITEMS => $this->getShipmentSelectedItemsIds($idSalesShipment),
            AddressForm::OPTION_SALUTATION_CHOICES => $this->getSalutationOptions(),
        ];

        $orderTransfer = $this->salesFacade->findOrderByIdSalesOrder($idSalesOrder);
        if ($orderTransfer === null) {
            return $options;
        }

        $options[ItemForm::OPTION_ORDER_ITEMS_CHOICES] = $this->getOrderItemsOptions($orderTransfer);

        return $options;
    }

    /**
     * @param int $idSalesShipment
     *
     * @return \Generated\Shared\Transfer\ShipmentTransfer|null
     */
    public function findShipmentById(int $idSalesShipment): ?ShipmentTransfer
    {
        return $this->shipmentFacade->findShipmentById($idSalesShipment);
    }

    /**
     * @param int|null $idSalesShipment
     *
     * @return array
     */
    public function getShipmentSelectedItemsIds(?int $idSalesShipment): array
    {
        if ($idSalesShipment === null) {
            return [];
        }

        $salesItems = $this->salesFacade->findSalesOrderItemsIdsBySalesShipmentId($idSalesShipment);
        if ($salesItems->count() === 0) {
            return [];
        }

        $itemsIds = [];
        foreach ($salesItems as $item) {
            $idSalesOrderItem = $item->getIdSalesOrderItem();
            if ($idSalesOrderItem === null) {
                continue;
            }

            $itemsIds[] = $item->getIdSalesOrderItem();
        }

        return $itemsIds;
    }

    /**
     * @return null[]
     */
    protected function getAddressDefaultFields(): array
    {
        return [
            AddressForm::ADDRESS_FIELD_SALUTATION => null,
            AddressForm::ADDRESS_FIELD_FIRST_NAME => null,
            AddressForm::ADDRESS_FIELD_MIDDLE_NAME => null,
            AddressForm::ADDRESS_FIELD_LAST_NAME => null,
            AddressForm::ADDRESS_FIELD_EMAIL => null,
            AddressForm::ADDRESS_FIELD_ISO_2_CODE => null,
            AddressForm::ADDRESS_FIELD_ADDRESS_1 => null,
            AddressForm::ADDRESS_FIELD_ADDRESS_2 => null,
            AddressForm::ADDRESS_FIELD_COMPANY => null,
            AddressForm::ADDRESS_FIELD_CITY => null,
            AddressForm::ADDRESS_FIELD_ZIP_CODE => null,
            AddressForm::ADDRESS_FIELD_PO_BOX => null,
            AddressForm::ADDRESS_FIELD_PHONE => null,
            AddressForm::ADDRESS_FIELD_CELL_PHONE => null,
            AddressForm::ADDRESS_FIELD_DESCRIPTION => null,
            AddressForm::ADDRESS_FIELD_COMMENT => null,
        ];
    }

    /**
     * @param int $idSalesOrder
     *
     * @return array[]
     */
    protected function getItemsDefaultFields(int $idSalesOrder): array
    {
        $orderTransfer = $this->salesFacade->findOrderByIdSalesOrder($idSalesOrder);
        if ($orderTransfer === null) {
            return [];
        }

        return [
            ShipmentFormCreate::FORM_SALES_ORDER_ITEMS => $this->getOrderItemsOptions($orderTransfer),
        ];
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return \Generated\Shared\Transfer\ItemTransfer[]
     */
    protected function getOrderItemsOptions(OrderTransfer $orderTransfer): array
    {
        $itemCollection = [];
        foreach ($orderTransfer->getItems() as $itemTransfer) {
            $idSalesOrderItem = $itemTransfer->getIdSalesOrderItem();
            if ($idSalesOrderItem === null) {
                continue;
            }

            $itemCollection[$idSalesOrderItem] = $itemTransfer;
        }

        return $itemCollection;
    }

    /**
     * @param int $idSalesOrder
     *
     * @return array
     */
    protected function getShippingAddressesOptions(int $idSalesOrder): array
    {
        $addresses = ['New address'];

        $orderTransfer = $this->salesFacade->findOrderByIdSalesOrder($idSalesOrder);
        if ($orderTransfer === null) {
            return $addresses;
        }

        $customerTransfer = $orderTransfer->getCustomer();
        if ($customerTransfer === null) {
            return $addresses;
        }

        $addressesTransfer = $this->customerFacade->getAddresses($customerTransfer);
        foreach ($addressesTransfer->getAddresses() as $addressTransfer) {
            $idCustomerAddress = $addressTransfer->getIdCustomerAddress();
            if ($idCustomerAddress === null) {
                continue;
            }

            $addresses[$idCustomerAddress] = $this->getAddressLabel($addressTransfer);
        }

        return $addresses;
    }

    /**
     * @param \Generated\Shared\Transfer\AddressTransfer $addressTransfer
     *
     * @return string
     */
    protected function getAddressLabel(AddressTransfer $addressTransfer): string
    {
        return sprintf(
            static::ADDRESS_LABEL_PATTERN,
            $addressTransfer->getSalutation(),
            $addressTransfer->getFirstName(),
            $addressTransfer->getLastName(),
            $addressTransfer->getAddress1(),
            $addressTransfer->getAddress2(),
            $addressTransfer->getZipCode(),
            $addressTransfer->getCity()
        );
    }

    /**
     * @return string[]
     */
    protected function getShippingMethodsOptions(): array
    {
        $shipmentMethodCollection = $this->shipmentFacade->getMethods();
        $shipmentMethodOptionNameCollection = [];
        foreach ($shipmentMethodCollection as $shipmentMethodTransfer) {
            $idShipmentMethod = $shipmentMethodTransfer->getIdShipmentMethod();
            if ($idShipmentMethod === null) {
                continue;
            }

            $shipmentMethodOptionNameCollection[$idShipmentMethod] = sprintf(
                static::SHIPMENT_METHODS_OPTIONS_NAMES_PATTERN,
                $shipmentMethodTransfer->getCarrierName(),
                $shipmentMethodTransfer->getName()
            );
        }

        return $shipmentMethodOptionNameCollection;
    }

    /**
     * @return string[]
     */
    protected function getSalutationOptions(): array
    {
        $salutation = SpyCustomerTableMap::getValueSet(SpyCustomerTableMap::COL_SALUTATION);
        if (!is_array($salutation) || empty($salutation)) {
            return [];
        }

        $combinedSalutation = array_combine($salutation, $salutation);
        if ($combinedSalutation === false) {
            return [];
        }

        return $combinedSalutation;
    }

    /**
     * @param array $formData
     *
     * @return \Generated\Shared\Transfer\ShipmentFormTransfer
     */
    public function mapFormDataToShipmentFormTransfer(array $formData): ShipmentFormTransfer
    {
        $shipmentFormTransfer = (new ShipmentFormTransfer())->fromArray($formData, true);

        $itemList = new ArrayObject();
        foreach ($formData[ShipmentFormTransfer::ITEMS] as $itemTransfer) {
            $itemList->append($itemTransfer);
        }

        return $shipmentFormTransfer->setItems($itemList);
    }
}