<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ShipmentGui\Communication\Controller;

use Generated\Shared\Transfer\ExpenseTransfer;
use Generated\Shared\Transfer\ShipmentGroupTransfer;
use Generated\Shared\Transfer\ShipmentTransfer;
use Spryker\Zed\Kernel\Communication\Controller\AbstractController;
use Spryker\Zed\ShipmentGui\Communication\Form\ShipmentFormCreate;
use Symfony\Component\HttpFoundation\Request;

/**
 * @method \Spryker\Zed\ShipmentGui\Communication\ShipmentGuiCommunicationFactory getFactory()
 */
class EditController extends AbstractController
{
    protected const PARAM_ID_SALES_ORDER = 'id-sales-order';
    protected const PARAM_ID_SALES_SHIPMENT = 'id-shipment';
    protected const PARAM_REDIRECT_URL = 'redirect-url';

    protected const REDIRECT_URL_DEFAULT = '/sales';

    protected const MESSAGE_SHIPMENT_EDIT_SUCCESS = 'Shipment has been successfully created.';
    protected const MESSAGE_SHIPMENT_EDIT_ERROR = 'Shipment create failed.';

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function indexAction(Request $request)
    {
        $redirectUrl = $request->get(static::PARAM_REDIRECT_URL, static::REDIRECT_URL_DEFAULT);

        $idSalesOrder = $request->query->get(static::PARAM_ID_SALES_ORDER);
        $idSalesShipment = $request->query->get(static::PARAM_ID_SALES_SHIPMENT);
        $dataProvider = $this->getFactory()->createShipmentFormEditDataProvider();

        $form = $this->getFactory()
            ->createShipmentFormEdit(
                $dataProvider->getData($idSalesOrder, $idSalesShipment),
                $dataProvider->getOptions($idSalesOrder, $idSalesShipment)
            )
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $shipmentGroupTransfer = $this->createShipmentGroupTransfer($form->getData());
            $orderTransfer = $this
                ->getFactory()
                ->getSalesFacade()
                ->findOrderByIdSalesOrder($idSalesOrder);

            if ($orderTransfer === null) {
                $this->addErrorMessage('Order does not exist');

                return $this->redirectResponse($redirectUrl);
            }

            $responseTransfer = $this->getFactory()
                ->getShipmentFacade()
                ->saveShipment($shipmentGroupTransfer, $orderTransfer);

            if ($responseTransfer->getIsSuccessful()) {
                $this->addSuccessMessage('Shipment has been updated successfully');
            }

            return $this->redirectResponse($redirectUrl);
        }

        return $this->viewResponse([
            'idSalesOrder' => $idSalesOrder,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param array $formData
     *
     * @return \Generated\Shared\Transfer\ShipmentGroupTransfer
     */
    protected function createShipmentGroupTransfer(array $formData): ShipmentGroupTransfer
    {
        $shipmentGroupTransfer = new ShipmentGroupTransfer();
        $shipmentGroupTransfer = $this->addShipmentTransfer($shipmentGroupTransfer, $formData);

        return $shipmentGroupTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\ShipmentGroupTransfer $shipmentGroupTransfer
     * @param array $formData
     *
     * @return \Generated\Shared\Transfer\ShipmentGroupTransfer
     */
    protected function addShipmentTransfer(ShipmentGroupTransfer $shipmentGroupTransfer, array $formData): ShipmentGroupTransfer
    {
        $shipmentTransfer = (new ShipmentTransfer())->fromArray($formData, true);
        $shipmentTransfer->requireShippingAddress();
        if ($formData['id_shipping_address']) {
            $shipmentTransfer->getShippingAddress()
                ->setIdSalesOrderAddress($formData['id_shipping_address']);
        }

        $shipmentMethodTransfer = $this
            ->getFactory()
            ->getShipmentFacade()
            ->findMethodById($formData[ShipmentFormCreate::FIELD_ID_SHIPMENT_METHOD]);
        $shipmentTransfer->setMethod($shipmentMethodTransfer);

        return $shipmentGroupTransfer->setShipment($shipmentTransfer);
    }
}
