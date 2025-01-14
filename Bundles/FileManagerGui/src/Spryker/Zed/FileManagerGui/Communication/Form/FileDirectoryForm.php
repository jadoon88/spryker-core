<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\FileManagerGui\Communication\Form;

use DateTime;
use Generated\Shared\Transfer\FileDirectoryTransfer;
use Spryker\Zed\Kernel\Communication\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @method \Spryker\Zed\FileManagerGui\Communication\FileManagerGuiCommunicationFactory getFactory()
 * @method \Spryker\Zed\FileManagerGui\FileManagerGuiConfig getConfig()
 */
class FileDirectoryForm extends AbstractType
{
    /**
     * @var string
     */
    public const FIELD_NAME = 'name';

    /**
     * @var string
     */
    public const FIELD_FILE_DIRECTORY_LOCALIZED_ATTRIBUTES = 'fileDirectoryLocalizedAttributes';

    /**
     * @var string
     */
    public const OPTION_DATA_CLASS = 'data_class';

    /**
     * @var string
     */
    public const OPTION_AVAILABLE_LOCALES = 'option_available_locales';

    /**
     * @var int
     */
    protected const FIELD_NAME_MAX_LENGTH = 255;

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     *
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired(static::OPTION_AVAILABLE_LOCALES);

        $resolver->setDefaults([
            static::OPTION_DATA_CLASS => FileDirectoryTransfer::class,
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
            ->addNameField($builder)
            ->addFileDirectoryLocalizedAttributesForms($builder, $options);
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addNameField(FormBuilderInterface $builder)
    {
        $builder->add(
            static::FIELD_NAME,
            TextType::class,
            [

                'constraints' => [
                    new NotBlank(),
                    new Length([
                        'max' => static::FIELD_NAME_MAX_LENGTH,
                    ]),
                ],
            ],
        );

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param mixed|null $options
     *
     * @return $this
     */
    protected function addFileDirectoryLocalizedAttributesForms(FormBuilderInterface $builder, $options = null)
    {
        $builder->add(static::FIELD_FILE_DIRECTORY_LOCALIZED_ATTRIBUTES, CollectionType::class, [
            'entry_type' => FileDirectoryLocalizedAttributesForm::class,
            'allow_add' => true,
            'allow_delete' => true,

            'entry_options' => [
                static::OPTION_AVAILABLE_LOCALES => $options[static::OPTION_AVAILABLE_LOCALES],
            ],
        ]);

        return $this;
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
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'file_directory';
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
