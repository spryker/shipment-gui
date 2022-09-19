<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ShipmentGui\Communication\Form\DataProvider;

use Generated\Shared\Transfer\ShipmentMethodTransfer;
use Spryker\Zed\ShipmentGui\Dependency\Facade\ShipmentGuiToLocaleFacadeInterface;
use Spryker\Zed\ShipmentGui\Dependency\Facade\ShipmentGuiToTaxFacadeInterface;

class ViewShipmentMethodFormDataProvider
{
    /**
     * @var string
     */
    public const OPTION_TAX_SET_CHOICES = 'option_tax_set_choices';

    /**
     * @var string
     */
    public const OPTION_STORE_RELATION_DISABLED = 'option_store_relation_disabled';

    /**
     * @var string
     */
    public const OPTION_PRICES_DISABLED = 'option_prices_disabled';

    /**
     * @var string
     */
    public const OPTION_TAX_SET_DISABLED = 'option_tax_set_disabled';

    /**
     * @var string
     */
    public const OPTION_LOCALE = 'locale';

    /**
     * @var \Spryker\Zed\ShipmentGui\Dependency\Facade\ShipmentGuiToTaxFacadeInterface
     */
    protected $taxFacade;

    /**
     * @var \Spryker\Zed\ShipmentGui\Dependency\Facade\ShipmentGuiToLocaleFacadeInterface
     */
    protected $localeFacade;

    /**
     * @param \Spryker\Zed\ShipmentGui\Dependency\Facade\ShipmentGuiToTaxFacadeInterface $taxFacade
     * @param \Spryker\Zed\ShipmentGui\Dependency\Facade\ShipmentGuiToLocaleFacadeInterface $localeFacade
     */
    public function __construct(
        ShipmentGuiToTaxFacadeInterface $taxFacade,
        ShipmentGuiToLocaleFacadeInterface $localeFacade
    ) {
        $this->taxFacade = $taxFacade;
        $this->localeFacade = $localeFacade;
    }

    /**
     * @param \Generated\Shared\Transfer\ShipmentMethodTransfer $shipmentMethodTransfer
     *
     * @return \Generated\Shared\Transfer\ShipmentMethodTransfer
     */
    public function getData(ShipmentMethodTransfer $shipmentMethodTransfer): ShipmentMethodTransfer
    {
        return $shipmentMethodTransfer;
    }

    /**
     * @return array<string, mixed>
     */
    public function getOptions(): array
    {
        return [
            static::OPTION_TAX_SET_CHOICES => $this->createTaxSetsList(),
            static::OPTION_STORE_RELATION_DISABLED => true,
            static::OPTION_PRICES_DISABLED => true,
            static::OPTION_TAX_SET_DISABLED => true,
            static::OPTION_LOCALE => $this->localeFacade->getCurrentLocaleName(),
        ];
    }

    /**
     * @return array
     */
    protected function createTaxSetsList()
    {
        $taxSetCollection = $this->taxFacade->getTaxSets();

        $taxSetList = [];
        foreach ($taxSetCollection->getTaxSets() as $taxSetTransfer) {
            $taxSetList[$taxSetTransfer->getIdTaxSet()] = $taxSetTransfer->getName();
        }

        return $taxSetList;
    }
}
