<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ShipmentGui\Dependency\Facade;

use Generated\Shared\Transfer\AddressesTransfer;
use Generated\Shared\Transfer\AddressTransfer;
use Generated\Shared\Transfer\CustomerTransfer;

class ShipmentGuiToCustomerFacadeBridge implements ShipmentGuiToCustomerFacadeInterface
{
    /**
     * @var \Spryker\Zed\Customer\Business\CustomerFacadeInterface
     */
    protected $customerFacade;

    /**
     * @param \Spryker\Zed\Customer\Business\CustomerFacadeInterface $customerFacade
     */
    public function __construct($customerFacade)
    {
        $this->customerFacade = $customerFacade;
    }

    public function getAddresses(CustomerTransfer $customerTransfer): AddressesTransfer
    {
        return $this->customerFacade->getAddresses($customerTransfer);
    }

    public function findCustomerAddressById(int $idCustomerAddress): ?AddressTransfer
    {
        return $this->customerFacade->findCustomerAddressById($idCustomerAddress);
    }

    public function findCustomerAddressByAddressData(AddressTransfer $addressTransfer): ?AddressTransfer
    {
        return $this->customerFacade->findCustomerAddressByAddressData($addressTransfer);
    }

    public function getAllSalutations(): array
    {
        return $this->customerFacade->getAllSalutations();
    }
}
