<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ShipmentGui\Communication\Expander;

use Generated\Shared\Transfer\OrderTransfer;

interface ShipmentOrderDetailDataExpanderInterface
{
    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     * @param array<string, mixed> $orderDetailData
     *
     * @return array<string, mixed>
     */
    public function expand(OrderTransfer $orderTransfer, array $orderDetailData): array;
}
