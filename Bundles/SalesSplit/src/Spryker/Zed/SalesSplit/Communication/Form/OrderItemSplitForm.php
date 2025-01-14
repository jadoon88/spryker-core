<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\SalesSplit\Communication\Form;

use Spryker\Zed\Kernel\Communication\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

/**
 * @method \Spryker\Zed\SalesSplit\Business\SalesSplitFacadeInterface getFacade()
 * @method \Spryker\Zed\SalesSplit\Communication\SalesSplitCommunicationFactory getFactory()
 * @method \Spryker\Zed\SalesSplit\SalesSplitConfig getConfig()
 */
class OrderItemSplitForm extends AbstractType
{
    /**
     * @var string
     */
    public const FIELD_QUANTITY = 'quantity';

    /**
     * @var string
     */
    public const FIELD_ID_ORDER_ITEM = 'id_sales_order_item';

    /**
     * @var string
     */
    public const FIELD_ID_ORDER = 'id_sales_order';

    /**
     * @var string
     */
    public const VALIDATE_MESSAGE_NUMERIC = 'Please provide numeric value.';

    /**
     * @var string
     */
    public const VALIDATION_MESSAGE_QUANTITY = 'Please provide quantity.';

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'orderItemSplit';
    }

    /**
     * @deprecated Use {@link getBlockPrefix()} instead.
     *
     * @return string
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array<string, mixed> $options
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->setAction('/sales-split/order-item-split/split');

        $this
            ->addQuantityField($builder)
            ->addIdOrderItemField($builder)
            ->addIdOrderField($builder)
            ->addSubmitButton($builder);
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addQuantityField(FormBuilderInterface $builder)
    {
        $builder->add(static::FIELD_QUANTITY, TextType::class, [
            'label' => 'Quantity',
            'constraints' => [
                new NotBlank([
                    'message' => static::VALIDATION_MESSAGE_QUANTITY,
                ]),
                new Regex([
                    'pattern' => '/^\d+$/',
                    'message' => static::VALIDATE_MESSAGE_NUMERIC,
                ]),
            ],
        ]);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addIdOrderItemField(FormBuilderInterface $builder)
    {
        $builder->add(static::FIELD_ID_ORDER_ITEM, HiddenType::class);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addIdOrderField(FormBuilderInterface $builder)
    {
        $builder->add(static::FIELD_ID_ORDER, HiddenType::class);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addSubmitButton(FormBuilderInterface $builder)
    {
        $builder->add('Split', SubmitType::class, [
            'attr' => [
                'class' => 'btn btn-sm btn-primary',
            ],
        ]);

        return $this;
    }
}
