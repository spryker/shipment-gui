<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ShipmentGui\Communication\Controller;

use Spryker\Zed\Kernel\Communication\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

/**
 * @method \Spryker\Zed\ShipmentGui\Communication\ShipmentGuiCommunicationFactory getFactory()
 */
class ViewShipmentMethodController extends AbstractController
{
    /**
     * @var string
     */
    protected const PARAM_ID_SHIPMENT_METHOD = 'id-shipment-method';

    /**
     * @var string
     */
    protected const REDIRECT_URL = '/shipment-gui/shipment-method';

    /**
     * @var string
     */
    protected const ERROR_MESSAGE_SHIPMENT_METHOD_NOT_FOUND = 'Shipment method is not found';

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|array
     */
    public function indexAction(Request $request)
    {
        $idShipmentMethod = $request->query->getInt(static::PARAM_ID_SHIPMENT_METHOD);

        $shipmentMethodTransfer = $this->getFactory()
            ->getShipmentFacade()
            ->findMethodById($idShipmentMethod);

        if ($shipmentMethodTransfer === null) {
            $this->addErrorMessage(static::ERROR_MESSAGE_SHIPMENT_METHOD_NOT_FOUND);

            return $this->redirectResponse(static::REDIRECT_URL);
        }

        $dataProvider = $this->getFactory()->createViewShipmentMethodFormDataProvider();
        $form = $this->getFactory()->createViewShipmentMethodForm(
            $dataProvider->getData($shipmentMethodTransfer),
            $dataProvider->getOptions(),
        );

        return $this->viewResponse([
            'form' => $form->createView(),
            'shipmentMethod' => $shipmentMethodTransfer,
        ]);
    }
}
