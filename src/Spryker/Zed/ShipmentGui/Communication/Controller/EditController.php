<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ShipmentGui\Communication\Controller;

use Generated\Shared\Transfer\SalesShipmentConditionsTransfer;
use Generated\Shared\Transfer\SalesShipmentCriteriaTransfer;
use Generated\Shared\Transfer\ShipmentGroupResponseTransfer;
use Spryker\Service\UtilText\Model\Url\Url;
use Spryker\Zed\Kernel\Communication\Controller\AbstractController;
use Spryker\Zed\ShipmentGui\Communication\Form\Item\ItemFormType;
use Spryker\Zed\ShipmentGui\Communication\Form\Shipment\ShipmentGroupFormType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @method \Spryker\Zed\ShipmentGui\Communication\ShipmentGuiCommunicationFactory getFactory()
 */
class EditController extends AbstractController
{
    /**
     * @var string
     */
    protected const PARAM_ID_SALES_ORDER = 'id-sales-order';

    /**
     * @var string
     */
    protected const PARAM_ID_SALES_SHIPMENT = 'id-shipment';

    /**
     * @var string
     */
    protected const REDIRECT_URL_DEFAULT = '/sales';

    /**
     * @var string
     */
    protected const REDIRECT_URL = '/sales/detail';

    /**
     * @var string
     */
    protected const MESSAGE_SHIPMENT_EDIT_SUCCESS = 'Shipment has been successfully edited.';

    /**
     * @var string
     */
    protected const MESSAGE_SHIPMENT_EDIT_FAIL = 'Shipment edit failed.';

    /**
     * @var string
     */
    protected const MESSAGE_ORDER_NOT_FOUND_ERROR = 'Sales order #%d not found.';

    /**
     * @var string
     */
    protected const MESSAGE_ORDER_SHIPMENT_NOT_FOUND_ERROR = 'Sales order shipment #%d not found.';

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|array
     */
    public function indexAction(Request $request)
    {
        $idSalesOrder = $request->query->getInt(static::PARAM_ID_SALES_ORDER);
        $idSalesShipment = $request->query->getInt(static::PARAM_ID_SALES_SHIPMENT);

        $orderTransfer = $this->getFactory()
            ->getSalesFacade()
            ->findOrderByIdSalesOrder($idSalesOrder);

        if ($orderTransfer === null) {
            $this->addErrorMessage(static::MESSAGE_ORDER_NOT_FOUND_ERROR, ['%d' => $idSalesOrder]);
            $redirectUrl = Url::generate(static::REDIRECT_URL_DEFAULT)->build();

            return $this->redirectResponse($redirectUrl);
        }

        $salesShipmentCriteriaTransfer = $this->createSalesShipmentCriteriaTransfer($idSalesShipment);
        $salesShipmentCollectionTransfer = $this->getFactory()
            ->getShipmentFacade()
            ->getSalesShipmentCollection($salesShipmentCriteriaTransfer);

        $shipmentTransfer = $salesShipmentCollectionTransfer->getShipments()->getIterator()->current();
        if ($shipmentTransfer === null) {
            $this->addErrorMessage(static::MESSAGE_ORDER_SHIPMENT_NOT_FOUND_ERROR, ['%d' => $idSalesShipment]);
            $redirectUrl = Url::generate(static::REDIRECT_URL_DEFAULT)->build();

            return $this->redirectResponse($redirectUrl);
        }

        $dataProvider = $this->getFactory()->createShipmentFormDataProvider();

        $form = $this->getFactory()
            ->createShipmentEditForm(
                $dataProvider->getData($orderTransfer, $shipmentTransfer),
                $dataProvider->getOptions($orderTransfer, $shipmentTransfer),
            )
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $shipmentGroupTransfer = $this->getFactory()
                ->getShipmentFacade()
                ->createShipmentGroupTransferWithListedItems($form->getData(), $this->getItemListUpdatedStatus($form));

            $responseTransfer = $this->getFactory()
                ->getShipmentFacade()
                ->saveShipment($shipmentGroupTransfer, $orderTransfer);

            $this->addStatusMessage($responseTransfer);

            $redirectUrl = Url::generate(static::REDIRECT_URL, [
                static::PARAM_ID_SALES_ORDER => $idSalesOrder,
            ])->build();

            return $this->redirectResponse($redirectUrl);
        }

        return $this->viewResponse([
            'idSalesOrder' => $idSalesOrder,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param \Symfony\Component\Form\FormInterface $form
     *
     * @return array<bool>
     */
    protected function getItemListUpdatedStatus(FormInterface $form): array
    {
        if (!$form->offsetExists(ShipmentGroupFormType::FIELD_SALES_ORDER_ITEMS_FORM)) {
            return [];
        }

        $itemFormTypeCollection = $form->get(ShipmentGroupFormType::FIELD_SALES_ORDER_ITEMS_FORM);
        $requestedItems = [];
        foreach ($itemFormTypeCollection as $itemFormType) {
            $itemTransfer = $itemFormType->getData();
            /** @var \Generated\Shared\Transfer\ItemTransfer $itemTransfer */
            $requestedItems[$itemTransfer->getIdSalesOrderItem()] = $itemFormType->get(ItemFormType::FIELD_IS_UPDATED)->getData();
        }

        return $requestedItems;
    }

    /**
     * @param \Generated\Shared\Transfer\ShipmentGroupResponseTransfer $responseTransfer
     *
     * @return void
     */
    protected function addStatusMessage(ShipmentGroupResponseTransfer $responseTransfer): void
    {
        if ($responseTransfer->getIsSuccessful()) {
            $this->addSuccessMessage(static::MESSAGE_SHIPMENT_EDIT_SUCCESS);

            return;
        }

        $this->addErrorMessage(static::MESSAGE_SHIPMENT_EDIT_FAIL);
    }

    /**
     * @param int $idSalesShipment
     *
     * @return \Generated\Shared\Transfer\SalesShipmentCriteriaTransfer
     */
    protected function createSalesShipmentCriteriaTransfer(int $idSalesShipment): SalesShipmentCriteriaTransfer
    {
        $shipmentConditionsTransfer = (new SalesShipmentConditionsTransfer())->addIdSalesShipment($idSalesShipment);

        return (new SalesShipmentCriteriaTransfer())->setSalesShipmentConditions($shipmentConditionsTransfer);
    }
}
