<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ShipmentGui\Communication\Plugin\Form;

use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\Kernel\Communication\Form\FormTypeInterface;
use Spryker\Zed\ShipmentGui\Communication\Form\Item\ItemFormType;

/**
 * @method \Spryker\Zed\ShipmentGui\ShipmentGuiConfig getConfig()
 * @method \Spryker\Zed\ShipmentGui\Communication\ShipmentGuiCommunicationFactory getFactory()
 */
class ItemFormTypePlugin extends AbstractPlugin implements FormTypeInterface
{
    /**
     * {@inheritDoc}
     * - Returns ItemFormType class name resolution.
     *
     * @api
     *
     * @return string
     */
    public function getType(): string
    {
        return ItemFormType::class;
    }
}
