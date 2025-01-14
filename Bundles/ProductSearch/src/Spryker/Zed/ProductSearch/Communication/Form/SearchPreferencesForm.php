<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductSearch\Communication\Form;

use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @method \Spryker\Zed\ProductSearch\Business\ProductSearchFacadeInterface getFacade()
 * @method \Spryker\Zed\ProductSearch\Communication\ProductSearchCommunicationFactory getFactory()
 * @method \Spryker\Zed\ProductSearch\Persistence\ProductSearchQueryContainerInterface getQueryContainer()
 * @method \Spryker\Zed\ProductSearch\ProductSearchConfig getConfig()
 * @method \Spryker\Zed\ProductSearch\Persistence\ProductSearchRepositoryInterface getRepository()
 */
class SearchPreferencesForm extends AbstractAttributeKeyForm
{
    /**
     * @var string
     */
    public const FIELD_ID_PRODUCT_ATTRIBUTE_KEY = 'idProductAttributeKey';

    /**
     * @var string
     */
    public const FIELD_FULL_TEXT = 'fullText';

    /**
     * @var string
     */
    public const FIELD_FULL_TEXT_BOOSTED = 'fullTextBoosted';

    /**
     * @var string
     */
    public const FIELD_SUGGESTION_TERMS = 'suggestionTerms';

    /**
     * @var string
     */
    public const FIELD_COMPLETION_TERMS = 'completionTerms';

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'searchPreferences';
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
        $this
            ->addIdProductAttributeKeyField($builder)
            ->addKeyField($builder, $options)
            ->addFullTextField($builder)
            ->addFullTextBoostedField($builder)
            ->addSuggestionTermsField($builder)
            ->addCompletionTermsField($builder);
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addIdProductAttributeKeyField(FormBuilderInterface $builder)
    {
        $builder->add(static::FIELD_ID_PRODUCT_ATTRIBUTE_KEY, HiddenType::class);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array<string, mixed> $options
     *
     * @return $this
     */
    protected function addKeyField(FormBuilderInterface $builder, array $options)
    {
        $builder->add(static::FIELD_KEY, TextType::class, [
            'label' => 'Attribute key',
            'constraints' => $this->createAttributeKeyFieldConstraints(),
            'disabled' => $options[static::OPTION_IS_UPDATE],
        ]);

        return $this;
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    protected function isUniqueKey($key)
    {
        $keyCount = $this->getQueryContainer()
            ->queryProductAttributeKey()
            ->joinSpyProductSearchAttributeMap()
            ->filterByKey($key)
            ->count();

        return ($keyCount === 0);
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addFullTextField(FormBuilderInterface $builder)
    {
        $builder->add(static::FIELD_FULL_TEXT, ChoiceType::class, [
            'choices' => array_flip($this->getYesNoChoices()),
        ]);

        $this->addBoolModelTransformer($builder, static::FIELD_FULL_TEXT);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addFullTextBoostedField(FormBuilderInterface $builder)
    {
        $builder->add(static::FIELD_FULL_TEXT_BOOSTED, ChoiceType::class, [
            'choices' => array_flip($this->getYesNoChoices()),
        ]);

        $this->addBoolModelTransformer($builder, static::FIELD_FULL_TEXT_BOOSTED);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addSuggestionTermsField(FormBuilderInterface $builder)
    {
        $builder->add(static::FIELD_SUGGESTION_TERMS, ChoiceType::class, [
            'choices' => array_flip($this->getYesNoChoices()),
        ]);

        $this->addBoolModelTransformer($builder, static::FIELD_SUGGESTION_TERMS);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addCompletionTermsField(FormBuilderInterface $builder)
    {
        $builder->add(static::FIELD_COMPLETION_TERMS, ChoiceType::class, [
            'choices' => array_flip($this->getYesNoChoices()),
        ]);

        $this->addBoolModelTransformer($builder, static::FIELD_COMPLETION_TERMS);

        return $this;
    }

    /**
     * @return array
     */
    protected function getYesNoChoices()
    {
        return [
            'no' => 'No',
            'yes' => 'Yes',
        ];
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param string $fieldName
     *
     * @return void
     */
    protected function addBoolModelTransformer(FormBuilderInterface $builder, $fieldName)
    {
        $builder->get($fieldName)->addModelTransformer(new CallbackTransformer(
            function ($originalValue) {
                return $originalValue ? 'yes' : 'no';
            },
            function ($submittedValue) {
                return $submittedValue === 'yes' ? true : false;
            },
        ));
    }
}
