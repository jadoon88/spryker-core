<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Customer\Communication\Form;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @method \Spryker\Zed\Customer\Business\CustomerFacadeInterface getFacade()
 * @method \Spryker\Zed\Customer\Communication\CustomerCommunicationFactory getFactory()
 * @method \Spryker\Zed\Customer\Persistence\CustomerQueryContainerInterface getQueryContainer()
 * @method \Spryker\Zed\Customer\CustomerConfig getConfig()
 * @method \Spryker\Zed\Customer\Persistence\CustomerRepositoryInterface getRepository()
 */
class CustomerUpdateForm extends CustomerForm
{
    /**
     * @var string
     */
    public const FIELD_DEFAULT_BILLING_ADDRESS = 'default_billing_address';

    /**
     * @var string
     */
    public const FIELD_DEFAULT_SHIPPING_ADDRESS = 'default_shipping_address';

    /**
     * @var string
     */
    public const OPTION_ADDRESS_CHOICES = 'address_choices';

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     *
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setRequired(static::OPTION_ADDRESS_CHOICES);
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array<string, mixed> $options
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $this
            ->addDefaultBillingAddressField($builder, $options[static::OPTION_ADDRESS_CHOICES])
            ->addDefaultShippingAddressField($builder, $options[static::OPTION_ADDRESS_CHOICES]);
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addEmailField(FormBuilderInterface $builder)
    {
        $builder->add(static::FIELD_EMAIL, EmailType::class, [
            'label' => 'Email',
            'constraints' => $this->createEmailConstraints(),
            'disabled' => 'disabled',
        ]);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $choices
     *
     * @return $this
     */
    protected function addDefaultBillingAddressField(FormBuilderInterface $builder, array $choices)
    {
        $builder->add(static::FIELD_DEFAULT_BILLING_ADDRESS, ChoiceType::class, [
            'label' => 'Billing Address',
            'placeholder' => 'Select one',
            'choices' => array_flip($choices),
            'required' => false,
        ]);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $choices
     *
     * @return $this
     */
    protected function addDefaultShippingAddressField(FormBuilderInterface $builder, array $choices)
    {
        $builder->add(static::FIELD_DEFAULT_SHIPPING_ADDRESS, ChoiceType::class, [
            'label' => 'Shipping Address',
            'placeholder' => 'Select one',
            'choices' => array_flip($choices),
            'required' => false,
        ]);

        return $this;
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'customer';
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
