<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ShipmentGui\Dependency\Facade;

use Generated\Shared\Transfer\TaxSetCollectionTransfer;

interface ShipmentGuiToTaxFacadeInterface
{
    public function getTaxSets(): TaxSetCollectionTransfer;
}
