<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ShipmentGui\Communication;

use Generated\Shared\Transfer\ShipmentGroupTransfer;
use Generated\Shared\Transfer\ShipmentMethodTransfer;
use Orm\Zed\Shipment\Persistence\SpyShipmentMethodQuery;
use Spryker\Zed\Kernel\Communication\AbstractCommunicationFactory;
use Spryker\Zed\Kernel\Communication\Form\FormTypeInterface;
use Spryker\Zed\ShipmentGui\Communication\Form\DataProvider\ShipmentCarrierFormDataProvider;
use Spryker\Zed\ShipmentGui\Communication\Form\DataProvider\ShipmentFormDataProvider;
use Spryker\Zed\ShipmentGui\Communication\Form\DataProvider\ShipmentMethodFormDataProvider;
use Spryker\Zed\ShipmentGui\Communication\Form\DataProvider\ViewShipmentMethodFormDataProvider;
use Spryker\Zed\ShipmentGui\Communication\Form\Shipment\ShipmentGroupFormType;
use Spryker\Zed\ShipmentGui\Communication\Form\Shipment\ShipmentMethodDeleteForm;
use Spryker\Zed\ShipmentGui\Communication\Form\ShipmentCarrier\ShipmentCarrierFormType;
use Spryker\Zed\ShipmentGui\Communication\Form\ShipmentMethod\Constraint\ShipmentMethodKeyUniqueConstraint;
use Spryker\Zed\ShipmentGui\Communication\Form\ShipmentMethod\Constraint\ShipmentMethodNameUniqueConstraint;
use Spryker\Zed\ShipmentGui\Communication\Form\ShipmentMethod\ShipmentMethodForm;
use Spryker\Zed\ShipmentGui\Communication\Form\ShipmentMethod\ViewShipmentMethodForm;
use Spryker\Zed\ShipmentGui\Communication\Form\Transformer\StringToNumberTransformer;
use Spryker\Zed\ShipmentGui\Communication\Grouper\ProductBundleGrouper;
use Spryker\Zed\ShipmentGui\Communication\Grouper\ProductBundleGrouperInterface;
use Spryker\Zed\ShipmentGui\Communication\Mapper\ShipmentCarrierMapper;
use Spryker\Zed\ShipmentGui\Communication\Provider\ShipmentOrderItemTemplateProvider;
use Spryker\Zed\ShipmentGui\Communication\Provider\ShipmentOrderItemTemplateProviderInterface;
use Spryker\Zed\ShipmentGui\Communication\Table\ShipmentMethodTable;
use Spryker\Zed\ShipmentGui\Communication\Tabs\ShipmentMethodTabs;
use Spryker\Zed\ShipmentGui\Dependency\Facade\ShipmentGuiToCustomerFacadeInterface;
use Spryker\Zed\ShipmentGui\Dependency\Facade\ShipmentGuiToLocaleFacadeInterface;
use Spryker\Zed\ShipmentGui\Dependency\Facade\ShipmentGuiToSalesFacadeInterface;
use Spryker\Zed\ShipmentGui\Dependency\Facade\ShipmentGuiToShipmentFacadeInterface;
use Spryker\Zed\ShipmentGui\Dependency\Facade\ShipmentGuiToTaxFacadeInterface;
use Spryker\Zed\ShipmentGui\ShipmentGuiDependencyProvider;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\FormInterface;

/**
 * @method \Spryker\Zed\ShipmentGui\ShipmentGuiConfig getConfig()
 */
class ShipmentGuiCommunicationFactory extends AbstractCommunicationFactory
{
    public function createShipmentFormDataProvider(): ShipmentFormDataProvider
    {
        return new ShipmentFormDataProvider(
            $this->getSalesFacade(),
            $this->getCustomerFacade(),
            $this->getShipmentFacade(),
        );
    }

    public function createShipmentCarrierFormDataProvider(): ShipmentCarrierFormDataProvider
    {
        return new ShipmentCarrierFormDataProvider($this->getShipmentFacade());
    }

    public function createProductBundleGrouper(): ProductBundleGrouperInterface
    {
        return new ProductBundleGrouper();
    }

    public function createShipmentCarrierFormType(): FormInterface
    {
        $shipmentCarrierFormDataProvider = $this->createShipmentCarrierFormDataProvider();

        return $this->getFormFactory()->create(
            ShipmentCarrierFormType::class,
            $shipmentCarrierFormDataProvider->getData(),
            $shipmentCarrierFormDataProvider->getOptions(),
        );
    }

    public function createShipmentCreateForm(
        ShipmentGroupTransfer $shipmentGroupTransfer,
        array $formOptions = []
    ): FormInterface {
        return $this->getFormFactory()->create(ShipmentGroupFormType::class, $shipmentGroupTransfer, $formOptions);
    }

    /**
     * @param \Generated\Shared\Transfer\ShipmentMethodTransfer|null $data
     * @param array<string, mixed> $options
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    public function createViewShipmentMethodForm(?ShipmentMethodTransfer $data = null, array $options = []): FormInterface
    {
        return $this->getFormFactory()->create(ViewShipmentMethodForm::class, $data, $options);
    }

    public function createViewShipmentMethodFormDataProvider(): ViewShipmentMethodFormDataProvider
    {
        return new ViewShipmentMethodFormDataProvider(
            $this->getTaxFacade(),
            $this->getLocaleFacade(),
        );
    }

    public function createShipmentEditForm(
        ShipmentGroupTransfer $shipmentGroupTransfer,
        array $formOptions = []
    ): FormInterface {
        return $this->getFormFactory()->create(ShipmentGroupFormType::class, $shipmentGroupTransfer, $formOptions);
    }

    public function createShipmentMethodDeleteForm(): FormInterface
    {
        return $this->getFormFactory()->create(ShipmentMethodDeleteForm::class);
    }

    public function createStringToNumberTransformer(): DataTransformerInterface
    {
        return new StringToNumberTransformer();
    }

    public function createShipmentCarrierMapper(): ShipmentCarrierMapper
    {
        return new ShipmentCarrierMapper();
    }

    public function createShipmentMethodTable(): ShipmentMethodTable
    {
        return new ShipmentMethodTable($this->getShipmentMethodQuery());
    }

    public function createShipmentMethodTabs(): ShipmentMethodTabs
    {
        return new ShipmentMethodTabs();
    }

    public function createShipmentMethodFormDataProvider(): ShipmentMethodFormDataProvider
    {
        return new ShipmentMethodFormDataProvider(
            $this->getShipmentFacade(),
            $this->getTaxFacade(),
            $this->getLocaleFacade(),
        );
    }

    /**
     * @param \Generated\Shared\Transfer\ShipmentMethodTransfer $data
     * @param array<string, mixed> $options
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    public function createShipmentMethodForm(ShipmentMethodTransfer $data, $options = []): FormInterface
    {
        return $this->getFormFactory()->create(ShipmentMethodForm::class, $data, $options);
    }

    public function createShipmentMethodNameUniqueConstraint(): ShipmentMethodNameUniqueConstraint
    {
        return new ShipmentMethodNameUniqueConstraint([
            ShipmentMethodNameUniqueConstraint::OPTION_SHIPMENT_FACADE => $this->getShipmentFacade(),
        ]);
    }

    public function createShipmentMethodKeyUniqueConstraint(): ShipmentMethodKeyUniqueConstraint
    {
        return new ShipmentMethodKeyUniqueConstraint([
            ShipmentMethodKeyUniqueConstraint::OPTION_SHIPMENT_FACADE => $this->getShipmentFacade(),
        ]);
    }

    public function createShipmentOrderItemTemplateProvider(): ShipmentOrderItemTemplateProviderInterface
    {
        return new ShipmentOrderItemTemplateProvider($this->getShipmentOrderItemTemplatePlugins());
    }

    public function getShipmentMethodQuery(): SpyShipmentMethodQuery
    {
        return $this->getProvidedDependency(ShipmentGuiDependencyProvider::PROPEL_QUERY_SHIPMENT_METHOD);
    }

    public function getSalesFacade(): ShipmentGuiToSalesFacadeInterface
    {
        return $this->getProvidedDependency(ShipmentGuiDependencyProvider::FACADE_SALES);
    }

    public function getShipmentFacade(): ShipmentGuiToShipmentFacadeInterface
    {
        return $this->getProvidedDependency(ShipmentGuiDependencyProvider::FACADE_SHIPMENT);
    }

    public function getCustomerFacade(): ShipmentGuiToCustomerFacadeInterface
    {
        return $this->getProvidedDependency(ShipmentGuiDependencyProvider::FACADE_CUSTOMER);
    }

    /**
     * @return \Spryker\Zed\ShipmentGui\Dependency\Service\ShipmentGuiToShipmentServiceInterface
     */
    public function getShipmentService()
    {
        return $this->getProvidedDependency(ShipmentGuiDependencyProvider::SERVICE_SHIPMENT);
    }

    public function getMoneyCollectionFormTypePlugin(): FormTypeInterface
    {
        return $this->getProvidedDependency(ShipmentGuiDependencyProvider::PLUGIN_MONEY_COLLECTION_FORM_TYPE);
    }

    public function getStoreRelationFormTypePlugin(): FormTypeInterface
    {
        return $this->getProvidedDependency(ShipmentGuiDependencyProvider::PLUGIN_STORE_RELATION_FORM_TYPE);
    }

    public function getTaxFacade(): ShipmentGuiToTaxFacadeInterface
    {
        return $this->getProvidedDependency(ShipmentGuiDependencyProvider::FACADE_TAX);
    }

    public function getLocaleFacade(): ShipmentGuiToLocaleFacadeInterface
    {
        return $this->getProvidedDependency(ShipmentGuiDependencyProvider::FACADE_LOCALE);
    }

    /**
     * @return array<\Spryker\Zed\ShipmentGuiExtension\Dependency\Plugin\ShipmentOrderItemTemplatePluginInterface>
     */
    public function getShipmentOrderItemTemplatePlugins(): array
    {
        return $this->getProvidedDependency(ShipmentGuiDependencyProvider::PLUGIN_SHIPMENT_ORDER_ITEM_TEMPLATE);
    }
}
