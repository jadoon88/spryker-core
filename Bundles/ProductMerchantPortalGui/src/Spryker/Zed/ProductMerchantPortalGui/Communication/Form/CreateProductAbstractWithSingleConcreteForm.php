<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Spryker Marketplace License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductMerchantPortalGui\Communication\Form;

use Spryker\Zed\Kernel\Communication\Form\AbstractType;
use Spryker\Zed\ProductMerchantPortalGui\Communication\Form\Constraint\SkuRegexConstraint;
use Spryker\Zed\ProductMerchantPortalGui\Communication\Form\Constraint\UniqueAbstractSkuConstraint;
use Spryker\Zed\ProductMerchantPortalGui\Communication\Form\Constraint\UniqueConcreteSkuConstraint;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @method \Spryker\Zed\ProductMerchantPortalGui\ProductMerchantPortalGuiConfig getConfig()
 * @method \Spryker\Zed\ProductMerchantPortalGui\Persistence\ProductMerchantPortalGuiRepositoryInterface getRepository()
 * @method \Spryker\Zed\ProductMerchantPortalGui\Communication\ProductMerchantPortalGuiCommunicationFactory getFactory()
 */
class CreateProductAbstractWithSingleConcreteForm extends AbstractType
{
    /**
     * @var string
     */
    protected const FIELD_NAME = 'name';

    /**
     * @var string
     */
    protected const FIELD_SKU = 'sku';

    /**
     * @var string
     */
    protected const FIELD_CONCRETE_NAME = 'concreteName';

    /**
     * @var string
     */
    protected const FIELD_CONCRETE_SKU = 'concreteSku';

    /**
     * @var string
     */
    protected const FIELD_AUTOGENERATE_SKU = 'autogenerateSku';

    /**
     * @var string
     */
    protected const FIELD_USE_ABSTRACT_PRODUCT_NAME = 'useAbstractProductName';

    /**
     * @var string
     */
    protected const LABEL_CONCRETE_SKU = 'Concrete Product SKU';

    /**
     * @var string
     */
    protected const LABEL_CONCRETE_NAME = 'Concrete Product Name';

    /**
     * @var string
     */
    protected const LABEL_AUTOGENERATE_SKU = 'Autogenerate SKU';

    /**
     * @var string
     */
    protected const LABEL_USE_ABSTRACT_PRODUCT_NAME = 'Same as Abstract Product';

    /**
     * @var string
     */
    protected const PLACEHOLDER_CONCRETE_SKU = 'Enter SKU';

    /**
     * @var string
     */
    protected const PLACEHOLDER_CONCRETE_NAME = 'Enter name';

    /**
     * @var string
     */
    protected const AUTOGENERATED_SKU_SUFFIX = '-1';

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface<mixed> $builder
     * @param array<mixed> $options
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $this->addSkuField($builder)
            ->addNameField($builder)
            ->addConcreteSkuField($builder)
            ->addConcreteNameField($builder)
            ->addAutogenerateSkuField($builder)
            ->addUseAbstractProductNameField($builder)
            ->addEventListener($builder);
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addSkuField(FormBuilderInterface $builder)
    {
        $builder->add(static::FIELD_SKU, HiddenType::class, [
            'required' => true,
            'constraints' => [
                new NotBlank(),
                new SkuRegexConstraint(),
                new UniqueAbstractSkuConstraint(),
            ],
        ]);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addNameField(FormBuilderInterface $builder)
    {
        $builder->add(static::FIELD_NAME, HiddenType::class, [
            'required' => true,
            'constraints' => [
                new NotBlank(),
            ],
        ]);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addConcreteSkuField(FormBuilderInterface $builder)
    {
        $builder->add(static::FIELD_CONCRETE_SKU, TextType::class, [
            'label' => static::LABEL_CONCRETE_SKU,
            'required' => true,
            'constraints' => [
                new NotBlank(),
                new SkuRegexConstraint(),
                new UniqueConcreteSkuConstraint(),
            ],
            'attr' => [
                'placeholder' => static::PLACEHOLDER_CONCRETE_SKU,
            ],
        ]);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addConcreteNameField(FormBuilderInterface $builder)
    {
        $builder->add(static::FIELD_CONCRETE_NAME, TextType::class, [
            'label' => static::LABEL_CONCRETE_NAME,
            'required' => true,
            'constraints' => [
                new NotBlank(),
            ],
            'attr' => [
                'placeholder' => static::PLACEHOLDER_CONCRETE_NAME,
            ],
        ]);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addAutogenerateSkuField(FormBuilderInterface $builder)
    {
        $builder->add(static::FIELD_AUTOGENERATE_SKU, CheckboxType::class, [
            'required' => false,
            'label' => static::LABEL_AUTOGENERATE_SKU,
        ]);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addUseAbstractProductNameField(FormBuilderInterface $builder)
    {
        $builder->add(static::FIELD_USE_ABSTRACT_PRODUCT_NAME, CheckboxType::class, [
            'required' => false,
            'label' => static::LABEL_USE_ABSTRACT_PRODUCT_NAME,
        ]);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return void
     */
    protected function addEventListener(FormBuilderInterface $builder): void
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $data = $event->getData();

            if ($data[static::FIELD_CONCRETE_SKU] !== $data[static::FIELD_SKU]) {
                return;
            }

            $data[static::FIELD_CONCRETE_SKU] = $data[static::FIELD_SKU] . static::AUTOGENERATED_SKU_SUFFIX;

            $event->setData($data);
        });
    }
}
