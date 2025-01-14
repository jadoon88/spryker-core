<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Spryker Marketplace License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MerchantProfileMerchantPortalGui\Communication\Form;

use Spryker\Zed\Kernel\Communication\Form\AbstractType;
use Spryker\Zed\MerchantProfileMerchantPortalGui\Communication\Form\Constraint\UniqueEmail;
use Spryker\Zed\MerchantProfileMerchantPortalGui\Communication\Form\Constraint\UniqueMerchantReference;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

/**
 * @method \Spryker\Zed\MerchantProfileMerchantPortalGui\MerchantProfileMerchantPortalGuiConfig getConfig()
 * @method \Spryker\Zed\MerchantProfileMerchantPortalGui\Communication\MerchantProfileMerchantPortalGuiCommunicationFactory getFactory()
 */
class BusinessInfoMerchantProfileForm extends AbstractType
{
    /**
     * @var string
     */
    protected const FIELD_ID_MERCHANT = 'id_merchant';

    /**
     * @var string
     */
    protected const FIELD_CONTACT_PERSON_TITLE = 'contact_person_title';

    /**
     * @var string
     */
    protected const FIELD_CONTACT_PERSON_FIRST_NAME = 'contact_person_first_name';

    /**
     * @var string
     */
    protected const FIELD_CONTACT_PERSON_LAST_NAME = 'contact_person_last_name';

    /**
     * @var string
     */
    protected const FIELD_CONTACT_PERSON_ROLE = 'contact_person_role';

    /**
     * @var string
     */
    protected const FIELD_NAME = 'name';

    /**
     * @var string
     */
    protected const FIELD_REGISTRATION_NUMBER = 'registration_number';

    /**
     * @var string
     */
    protected const FIELD_EMAIL = 'email';

    /**
     * @var string
     */
    protected const FIELD_MERCHANT_REFERENCE = 'merchant_reference';

    /**
     * @var string
     */
    protected const FIELD_MERCHANT_PROFILE = 'merchantProfile';

    /**
     * @var string
     */
    protected const FIELD_CONTACT_PERSON_PHONE = 'contact_person_phone';

    /**
     * @var string
     */
    protected const LABEL_CONTACT_PERSON_TITLE = 'Title';

    /**
     * @var string
     */
    protected const LABEL_CONTACT_PERSON_FIRST_NAME = 'First Name';

    /**
     * @var string
     */
    protected const LABEL_CONTACT_PERSON_LAST_NAME = 'Last Name';

    /**
     * @var string
     */
    protected const LABEL_CONTACT_PERSON_ROLE = 'Role';

    /**
     * @var string
     */
    protected const LABEL_NAME = 'Company Name';

    /**
     * @var string
     */
    protected const LABEL_REGISTRATION_NUMBER = 'Registration number';

    /**
     * @var string
     */
    protected const LABEL_EMAIL = 'Email';

    /**
     * @var string
     */
    protected const LABEL_MERCHANT_REFERENCE = 'Merchant Reference';

    /**
     * @var string
     */
    protected const LABEL_CONTACT_PERSON_PHONE = 'Phone Number';

    /**
     * @var string
     */
    protected const PATTERN_FIRST_NAME = '/^[^:\/<>]+$/';

    /**
     * @var string
     */
    protected const PATTERN_LAST_NAME = '/^[^:\/<>]+$/';

    /**
     * @return string
     */
    public function getBlockPrefix(): string
    {
        return 'businessInfoMerchantProfile';
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array<string, mixed> $options
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $this->addContactPersonTitleField($builder)
            ->addContactPersonFirstNameField($builder)
            ->addContactPersonLastNameField($builder)
            ->addContactPersonRoleField($builder)
            ->addNameField($builder)
            ->addEmailField($builder)
            ->addRegistrationNumberField($builder)
            ->addMerchantReferenceField($builder)
            ->addContactPersonPhoneField($builder);
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $choices
     *
     * @return $this
     */
    protected function addContactPersonTitleField(FormBuilderInterface $builder, array $choices = [])
    {
        $builder->add(static::FIELD_CONTACT_PERSON_TITLE, ChoiceType::class, [
            'choices' => array_flip($this->getConfig()->getSalutationChoices()),
            'required' => false,
            'label' => static::LABEL_CONTACT_PERSON_TITLE,
            'placeholder' => 'select.default.placeholder',
            'property_path' => 'merchantProfile.contactPersonTitle',

        ]);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addContactPersonFirstNameField(FormBuilderInterface $builder)
    {
        $builder->add(static::FIELD_CONTACT_PERSON_FIRST_NAME, TextType::class, [
            'label' => static::LABEL_CONTACT_PERSON_FIRST_NAME,
            'constraints' => [
                $this->createNotBlankConstraint(),
                $this->createLengthConstraint(),
                $this->createFirstNameRegexConstraint(),
            ],
            'required' => true,
            'property_path' => 'merchantProfile.contactPersonFirstName',
        ]);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addContactPersonLastNameField(FormBuilderInterface $builder)
    {
        $builder->add(static::FIELD_CONTACT_PERSON_LAST_NAME, TextType::class, [
            'label' => static::LABEL_CONTACT_PERSON_LAST_NAME,
            'constraints' => [
                $this->createNotBlankConstraint(),
                $this->createLengthConstraint(),
                $this->createLastNameRegexConstraint(),
            ],
            'required' => true,
            'property_path' => 'merchantProfile.contactPersonLastName',
        ]);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addContactPersonRoleField(FormBuilderInterface $builder)
    {
        $builder->add(static::FIELD_CONTACT_PERSON_ROLE, TextType::class, [
            'label' => static::LABEL_CONTACT_PERSON_ROLE,
            'constraints' => $this->getTextFieldConstraints(),
            'required' => false,
            'property_path' => 'merchantProfile.contactPersonRole',
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
        $builder->add(static::FIELD_NAME, TextType::class, [
            'label' => static::LABEL_NAME,
            'constraints' => [
                $this->createNotBlankConstraint(),
                $this->createLengthConstraint(),
            ],
        ]);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addRegistrationNumberField(FormBuilderInterface $builder)
    {
        $builder->add(static::FIELD_REGISTRATION_NUMBER, TextType::class, [
            'label' => static::LABEL_REGISTRATION_NUMBER,
            'required' => false,
            'constraints' => [
                new Length(['max' => 255]),
            ],
        ]);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addEmailField(FormBuilderInterface $builder)
    {
        $builder->add(static::FIELD_EMAIL, EmailType::class, [
            'label' => static::LABEL_EMAIL,
            'required' => true,
            'constraints' => $this->getEmailFieldConstraints($this->getCurrentIdFromFormData($builder)),
        ]);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addMerchantReferenceField(FormBuilderInterface $builder)
    {
        $builder
            ->add(static::FIELD_MERCHANT_REFERENCE, TextType::class, [
                'label' => static::LABEL_MERCHANT_REFERENCE,
                'required' => false,
                'constraints' => [
                    new Length([
                        'max' => 255,
                    ]),
                    new UniqueMerchantReference([
                        UniqueMerchantReference::OPTION_CURRENT_MERCHANT_ID => $this->getCurrentIdFromFormData($builder),
                    ]),
                ],
                'disabled' => true,
                'attr' => [
                    'read_only' => true,
                ],
            ]);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addContactPersonPhoneField(FormBuilderInterface $builder)
    {
        $builder->add(static::FIELD_CONTACT_PERSON_PHONE, TextType::class, [
            'label' => static::LABEL_CONTACT_PERSON_PHONE,
            'constraints' => $this->getTextFieldConstraints(),
            'required' => false,
            'property_path' => 'merchantProfile.contactPersonPhone',
        ]);

        return $this;
    }

    /**
     * @return array<\Symfony\Component\Validator\Constraint>
     */
    protected function getTextFieldConstraints(): array
    {
        return [
            new Length(['max' => 255]),
        ];
    }

    /**
     * @return \Symfony\Component\Validator\Constraints\NotBlank
     */
    protected function createNotBlankConstraint(): NotBlank
    {
        return new NotBlank();
    }

    /**
     * @return \Symfony\Component\Validator\Constraints\Length
     */
    protected function createLengthConstraint(): Length
    {
        return new Length(['max' => 255]);
    }

    /**
     * @return \Symfony\Component\Validator\Constraints\Regex
     */
    protected function createFirstNameRegexConstraint(): Regex
    {
        return new Regex([
            'pattern' => static::PATTERN_FIRST_NAME,
        ]);
    }

    /**
     * @return \Symfony\Component\Validator\Constraints\Regex
     */
    protected function createLastNameRegexConstraint(): Regex
    {
        return new Regex([
            'pattern' => static::PATTERN_LAST_NAME,
        ]);
    }

    /**
     * @param int|null $currentId
     *
     * @return array<\Symfony\Component\Validator\Constraint>
     */
    protected function getEmailFieldConstraints(?int $currentId = null): array
    {
        return [
            new NotBlank(),
            new Email(),
            new Length(['max' => 255]),
            new UniqueEmail([
                UniqueEmail::OPTION_CURRENT_ID_MERCHANT => $currentId,
            ]),
        ];
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $formBuilder
     *
     * @return int|null
     */
    protected function getCurrentIdFromFormData(FormBuilderInterface $formBuilder): ?int
    {
        /** @var \Generated\Shared\Transfer\MerchantTransfer|null $merchantTransfer */
        $merchantTransfer = $formBuilder->getForm()->getData();

        if (!$merchantTransfer) {
            return null;
        }

        return $merchantTransfer->getIdMerchant();
    }
}
