<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Spryker Marketplace License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductMerchantPortalGui\Communication\Form\Type;

use Generated\Shared\Transfer\ProductImageTransfer;
use Spryker\Zed\Kernel\Communication\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @method \Spryker\Zed\ProductMerchantPortalGui\ProductMerchantPortalGuiConfig getConfig()
 * @method \Spryker\Zed\ProductMerchantPortalGui\Persistence\ProductMerchantPortalGuiRepositoryInterface getRepository()
 * @method \Spryker\Zed\ProductMerchantPortalGui\Communication\ProductMerchantPortalGuiCommunicationFactory getFactory()
 */
class ProductImageFormType extends AbstractType
{
    /**
     * @var string
     */
    public const FIELD_ID_PRODUCT_IMAGE = 'idProductImage';

    /**
     * @var string
     */
    public const FIELD_SORT_ORDER = 'sortOrder';

    /**
     * @var string
     */
    public const FIELD_EXTERNAL_URL_SMALL = 'externalUrlSmall';

    /**
     * @var string
     */
    public const FIELD_EXTERNAL_URL_LARGE = 'externalUrlLarge';

    /**
     * @var string
     */
    protected const LABEL_SMALL_IMAGE_URL = 'Small Image URL';

    /**
     * @var string
     */
    protected const LABEL_LARGE_IMAGE_URL = 'Large Image URL';

    /**
     * @var int
     */
    protected const MAX_SORT_ORDER_VALUE = 2147483647; // 32 bit integer

    /**
     * @var int
     */
    protected const MIN_SORT_ORDER_VALUE = 0;

    /**
     * @var int
     */
    protected const DEFAULT_SORT_ORDER_VALUE = 0;

    /**
     * @var string
     */
    protected const MESSAGE_VALIDATION_NOT_BLANK_ERROR = 'The value cannot be blank. Please fill in this input';

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     *
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProductImageTransfer::class,
        ]);
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array<string, mixed> $options
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->addIdProductImage($builder)
            ->addImageSmallField($builder)
            ->addImageBigField($builder)
            ->addSortOrderField($builder);
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addIdProductImage(FormBuilderInterface $builder)
    {
        $builder->add(static::FIELD_ID_PRODUCT_IMAGE, HiddenType::class, []);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addImageSmallField(FormBuilderInterface $builder)
    {
        $builder->add(static::FIELD_EXTERNAL_URL_SMALL, TextType::class, [
            'constraints' => [
                new NotBlank(['message' => static::MESSAGE_VALIDATION_NOT_BLANK_ERROR]),
            ],
            'label' => static::LABEL_SMALL_IMAGE_URL,
            'required' => true,
        ]);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addImageBigField(FormBuilderInterface $builder)
    {
        $builder->add(static::FIELD_EXTERNAL_URL_LARGE, TextType::class, [
            'constraints' => [
                new NotBlank(['message' => static::MESSAGE_VALIDATION_NOT_BLANK_ERROR]),
            ],
            'label' => static::LABEL_LARGE_IMAGE_URL,
            'required' => true,
        ]);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addSortOrderField(FormBuilderInterface $builder)
    {
        $builder->add(static::FIELD_SORT_ORDER, NumberType::class, [
            'empty_data' => '0',
            'constraints' => [
                new LessThanOrEqual([
                    'value' => static::MAX_SORT_ORDER_VALUE,
                ]),
                new GreaterThanOrEqual([
                    'value' => static::MIN_SORT_ORDER_VALUE,
                ]),
            ],
            'attr' => [
                'data-sort-order' => static::DEFAULT_SORT_ORDER_VALUE,
            ],
        ]);

        return $this;
    }
}
