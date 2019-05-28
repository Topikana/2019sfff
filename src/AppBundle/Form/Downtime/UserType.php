<?php

namespace AppBundle\Form\Downtime;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class UserType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, array(
                'attr' => array(
                    'class' => 'form-control'
                ),
                'required' => false,

            ))
            ->add('email', EmailType::class, array(
                'attr' => array(
                    'class' => 'form-control'
                ),
                'required' => false
            ))
            ->add('dn', TextType::class, array(
                'attr' => array(
                    'class' => 'form-control'
                ),
                'disabled' => true
            ))
            ->add('subscriptions', CollectionType::class, array(
                'entry_type' => 'AppBundle\Form\Downtime\SubscriptionType',
                'label' => false,

                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,

                'prototype' => true,
                'prototype_name' => '_name_',
                'entry_options' => array(
                    'label' => false,
                ),
            ))
            ->add('save', SubmitType::class, array(
                'label' => 'Save rules specifications',
                'attr' => array(
                    'class' => 'btn btn-primary btn'
                )
            ))
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Downtime\User'
        ));
        parent::configureOptions($resolver);
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'user_form';
    }
}
