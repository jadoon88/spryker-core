<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\NavigationGui\Communication\Form;

use DateTime;
use Generated\Shared\Transfer\NavigationNodeTransfer;
use Spryker\Zed\Kernel\Communication\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContext;

/**
 * @method \Spryker\Zed\NavigationGui\Communication\NavigationGuiCommunicationFactory getFactory()
 * @method \Spryker\Zed\NavigationGui\Persistence\NavigationGuiQueryContainerInterface getQueryContainer()
 * @method \Spryker\Zed\NavigationGui\NavigationGuiConfig getConfig()
 */
class NavigationNodeFormType extends AbstractType
{
    /**
     * @var string
     */
    public const FIELD_NODE_TYPE = 'node_type';

    /**
     * @var string
     */
    public const FIELD_NAVIGATION_NODE_LOCALIZED_ATTRIBUTES = 'navigation_node_localized_attributes';

    /**
     * @var string
     */
    public const FIELD_IS_ACTIVE = 'is_active';

    /**
     * @var string
     */
    public const FIELD_VALID_FROM = 'valid_from';

    /**
     * @var string
     */
    public const FIELD_VALID_TO = 'valid_to';

    /**
     * @var string
     */
    public const NODE_TYPE_LABEL = 'label';

    /**
     * @var string
     */
    public const NODE_TYPE_CATEGORY = 'category';

    /**
     * @var string
     */
    public const NODE_TYPE_CMS_PAGE = 'cms_page';

    /**
     * @var string
     */
    public const NODE_TYPE_LINK = 'link';

    /**
     * @var string
     */
    public const NODE_TYPE_EXTERNAL_URL = 'external_url';

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     *
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'data_class' => NavigationNodeTransfer::class,
            'required' => false,
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
        $this
            ->addNodeTypeField($builder)
            ->addNavigationNodeLocalizedAttributesForms($builder)
            ->addIsActiveField($builder)
            ->addValidFromField($builder)
            ->addValidToField($builder);

        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
            $this->unsetLocalizedUrls($event, $event->getData());
        });
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addNodeTypeField(FormBuilderInterface $builder)
    {
        $builder
            ->add(static::FIELD_NODE_TYPE, ChoiceType::class, [
                'label' => 'Type',
                'choices' => array_flip([
                    static::NODE_TYPE_LABEL => 'Label',
                    static::NODE_TYPE_CATEGORY => 'Category',
                    static::NODE_TYPE_CMS_PAGE => 'CMS page',
                    static::NODE_TYPE_LINK => 'Link',
                    static::NODE_TYPE_EXTERNAL_URL => 'External URL',
                ]),
                'choice_attr' => [
                    'Category' => ['data-url' => '/search-for-category'],
                    'CMS page' => ['data-url' => '/search-for-cms'],
                    'Link' => [],
                    'External URL' => [],
                ],
            ]);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addNavigationNodeLocalizedAttributesForms(FormBuilderInterface $builder)
    {
        $builder
            ->add(static::FIELD_NAVIGATION_NODE_LOCALIZED_ATTRIBUTES, CollectionType::class, [
                'entry_type' => NavigationNodeLocalizedAttributesFormType::class,
            ]);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addIsActiveField(FormBuilderInterface $builder)
    {
        $builder
            ->add(static::FIELD_IS_ACTIVE, CheckboxType::class, [
                'label' => 'Active',
            ]);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addValidFromField(FormBuilderInterface $builder)
    {
        $builder->add(static::FIELD_VALID_FROM, DateType::class, [
            'label' => 'Valid from',
            'widget' => 'single_text',
            'required' => false,
            'attr' => [
                'class' => 'datepicker safe-datetime',
            ],
            'constraints' => [
                $this->createValidFromRangeConstraint(),
            ],
        ]);

        $builder->get(static::FIELD_VALID_FROM)
            ->addModelTransformer($this->createDateTimeModelTransformer());

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addValidToField(FormBuilderInterface $builder)
    {
        $builder->add(static::FIELD_VALID_TO, DateType::class, [
            'label' => 'Valid to',
            'widget' => 'single_text',
            'required' => false,
            'attr' => [
                'class' => 'datepicker safe-datetime',
            ],
            'constraints' => [
                $this->createValidToFieldRangeConstraint(),
            ],
        ]);

        $builder->get(static::FIELD_VALID_TO)
            ->addModelTransformer($this->createDateTimeModelTransformer());

        return $this;
    }

    /**
     * @return \Symfony\Component\Validator\Constraint
     */
    protected function createValidFromRangeConstraint()
    {
        return new Callback([
            'callback' => function ($dateTimeFrom, ExecutionContext $context) {
                $dateTimeTo = $context->getRoot()->getData()->getValidTo();
                if (!$dateTimeFrom || !$dateTimeTo) {
                    return;
                }

                if ($dateTimeFrom > $dateTimeTo) {
                    $context->addViolation('Date "Valid from" cannot be later than "Valid to".');
                }
            },
        ]);
    }

    /**
     * @return \Symfony\Component\Validator\Constraint
     */
    protected function createValidToFieldRangeConstraint()
    {
        return new Callback([
            'callback' => function ($dateTimeTo, ExecutionContext $context) {
                $dateTimeFrom = $context->getRoot()->getData()->getValidFrom();
                if (!$dateTimeFrom || !$dateTimeTo) {
                    return;
                }

                if ($dateTimeTo < $dateTimeFrom) {
                    $context->addViolation('Date "Valid to" cannot be earlier than "Valid from".');
                }
            },
        ]);
    }

    /**
     * @return \Symfony\Component\Form\CallbackTransformer
     */
    protected function createDateTimeModelTransformer()
    {
        return new CallbackTransformer(
            function ($value) {
                if ($value !== null) {
                    return new DateTime($value);
                }
            },
            function ($value) {
                return $value;
            },
        );
    }

    /**
     * @param \Symfony\Component\Form\FormEvent $event
     * @param \Generated\Shared\Transfer\NavigationNodeTransfer $navigationNodeTransfer
     *
     * @return void
     */
    public function unsetLocalizedUrls(FormEvent $event, NavigationNodeTransfer $navigationNodeTransfer)
    {
        if ($navigationNodeTransfer->getNodeType() === static::NODE_TYPE_CATEGORY || $navigationNodeTransfer->getNodeType() === static::NODE_TYPE_CMS_PAGE) {
            return;
        }

        foreach ($navigationNodeTransfer->getNavigationNodeLocalizedAttributes() as $navigationNodeLocalizedAttributesTransfer) {
            $navigationNodeLocalizedAttributesTransfer->setFkUrl(null);
        }

        $event->setData($navigationNodeTransfer);
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'navigation_node';
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
}
