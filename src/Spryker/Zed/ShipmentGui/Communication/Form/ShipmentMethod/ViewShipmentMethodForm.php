<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ShipmentGui\Communication\Form\ShipmentMethod;

use Generated\Shared\Transfer\ShipmentMethodTransfer;
use Spryker\Zed\Kernel\Communication\Form\AbstractType;
use Spryker\Zed\ShipmentGui\Communication\Form\DataProvider\ViewShipmentMethodFormDataProvider;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @method \Spryker\Zed\ShipmentGui\Communication\ShipmentGuiCommunicationFactory getFactory()
 * @method \Spryker\Zed\ShipmentGui\ShipmentGuiConfig getConfig()
 */
class ViewShipmentMethodForm extends AbstractType
{
    /**
     * @var string
     */
    public const FIELD_STORE_RELATION = 'storeRelation';

    /**
     * @var string
     */
    public const FIELD_PRICES = 'prices';

    /**
     * @var string
     */
    public const FIELD_TAX_SET = 'fkTaxSet';

    /**
     * @var string
     */
    public const OPTION_AMOUNT_PER_STORE = 'amount_per_store';

    /**
     * @return string
     */
    public function getBlockPrefix(): string
    {
        return 'shipment_method';
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     *
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'data_class' => ShipmentMethodTransfer::class,
            ViewShipmentMethodFormDataProvider::OPTION_LOCALE => null,
        ]);

        $resolver->setRequired([
            ViewShipmentMethodFormDataProvider::OPTION_TAX_SET_CHOICES,
            ViewShipmentMethodFormDataProvider::OPTION_STORE_RELATION_DISABLED,
            ViewShipmentMethodFormDataProvider::OPTION_PRICES_DISABLED,
            ViewShipmentMethodFormDataProvider::OPTION_TAX_SET_DISABLED,
        ]);
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array<string, mixed> $options
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $this->addStoreRelationForm($builder, $options)
            ->addPricesField($builder, $options)
            ->addTaxSetField($builder, $options);
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array<string, mixed> $options
     *
     * @return $this
     */
    protected function addStoreRelationForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            static::FIELD_STORE_RELATION,
            $this->getFactory()->getStoreRelationFormTypePlugin()->getType(),
            [
                'label' => false,
                'required' => false,
                'disabled' => $options[ViewShipmentMethodFormDataProvider::OPTION_STORE_RELATION_DISABLED],
            ],
        );

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array<string, mixed> $options
     *
     * @return $this
     */
    protected function addPricesField(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            static::FIELD_PRICES,
            $this->getFactory()->getMoneyCollectionFormTypePlugin()->getType(),
            [
                static::OPTION_AMOUNT_PER_STORE => true,
                'required' => false,
                'disabled' => $options[ViewShipmentMethodFormDataProvider::OPTION_PRICES_DISABLED],
            ],
        );

        $this->expandPricesFieldWithLocale($builder, $options);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array<string, mixed> $options
     *
     * @return $this
     */
    protected function addTaxSetField(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            static::FIELD_TAX_SET,
            ChoiceType::class,
            [
                'label' => 'Tax set',
                'placeholder' => 'Select one',
                'choices' => array_flip($options[ViewShipmentMethodFormDataProvider::OPTION_TAX_SET_CHOICES]),
                'disabled' => $options[ViewShipmentMethodFormDataProvider::OPTION_TAX_SET_DISABLED],
                'constraints' => [
                    new NotBlank(),
                ],
            ],
        );

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array<string, mixed> $options
     *
     * @return void
     */
    protected function expandPricesFieldWithLocale(FormBuilderInterface $builder, array $options): void
    {
        $builder->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) use ($options) {
            $pricesField = $event->getForm()->get(static::FIELD_PRICES);

            foreach ($pricesField->all() as $childIndex => $childField) {
                $childFieldConfig = $childField->getConfig();
                if (!$childFieldConfig->hasOption(ViewShipmentMethodFormDataProvider::OPTION_LOCALE)) {
                    continue;
                }

                $childFieldOptions = $childFieldConfig->getOptions();
                $childFieldOptions[ViewShipmentMethodFormDataProvider::OPTION_LOCALE] = $options[ViewShipmentMethodFormDataProvider::OPTION_LOCALE];

                $pricesField->remove($childIndex);
                $pricesField->add($childIndex, get_class($childFieldConfig->getType()->getInnerType()), $childFieldOptions);
            }
        });
    }
}
