<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ShipmentGui\Dependency\Facade;

use Generated\Shared\Transfer\AddressesTransfer;
use Generated\Shared\Transfer\AddressTransfer;
use Generated\Shared\Transfer\CustomerTransfer;

interface ShipmentGuiToCustomerFacadeInterface
{
    public function getAddresses(CustomerTransfer $customerTransfer): AddressesTransfer;

    public function findCustomerAddressById(int $idCustomerAddress): ?AddressTransfer;

    public function findCustomerAddressByAddressData(AddressTransfer $addressTransfer): ?AddressTransfer;

    public function getAllSalutations(): array;
}
