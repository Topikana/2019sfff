<?php

namespace AppBundle\Form\Downtime;

use AppBundle\Entity\Downtime\Subscription;
use Lavoisier\Hydrators\EntriesHydrator;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class SubscriptionType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */

    private $regions;
    private $sites;
    private $vo;

    public function __construct()
    {
        /**
         * @var $result \ArrayObject
         */
        $lavoisierUrl = 'cclavoisierfr.in2p3.fr';

        // liste des NGIS
        $lquery = new \Lavoisier\Query($lavoisierUrl, 'OPSCORE_NGI', 'lavoisier');
        $hydrator = new EntriesHydrator();
        $lquery->setHydrator($hydrator);
        $result = $lquery->execute();
        $NgiList = $result->getArrayCopy();

        $this->regions['All regions'] = 'ALL';

        foreach($NgiList as $region){
            $this->regions[$region["NAME"]] = $region["NAME"];
        }

        //liste des sites

        $this->sites['All sites'] = 'ALL';

        // liste des VO
        $lquery = new \Lavoisier\Query($lavoisierUrl, 'VAPOR_VO', 'lavoisier');
        $hydrator = new EntriesHydrator();
        $lquery->setHydrator($hydrator);
        $result = $lquery->execute();
        $voList = $result->getArrayCopy();

        $this->vo['All VOs'] = 'ALL';
        foreach($voList as $vo){
            $this->vo[$vo] = $vo;
        }


    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('rule', ChoiceType::class, array(
                    'choices'  => array(
                        'I WANT' => Subscription::I_WANT,
                        'I DON\'T WANT' => Subscription::I_DONT_WANT
                    ),
                    'attr' => array(
                        'class' => 'form-control input-sm'
                    ),
                    'required' => true,
                    'choice_label' => function ($key, $value) {
                        return preg_replace('#^\(ID: \d+\)\s+#', '', $value);
                    },
                    'choice_value' => function ($key) {
                        return $key;
                    },
                   // 'choices_as_values' => true

                )
            )
            ->add('region', ChoiceType::class, array(
                    'choices'  => $this->regions,
                    'attr' => array(
                        'class' => 'form-control input-sm',
                        'onchange'=> 'changeNGI(this)'
                    ),
                    'required' => true,
                    'placeholder' => 'Select your region',
                    'choice_label' => function ($key, $value) {
                        return preg_replace('#^\(ID: \d+\)\s+#', '', $value);
                    },
                    'choice_value' => function ($key) {
                        return $key;
                    },
                   // 'choices_as_values' => true
                )
            )
            ->add('site', ChoiceType::class, array(
                    'choices'  => $this->sites,
                    'attr' => array(
                        'class' => 'form-control input-sm',
                        'onchange'=> 'changeSite(this)'
                    ),
                    'placeholder' => false,
                    'required' => false,
                    'choice_label' => function ($key, $value) {
                        return preg_replace('#^\(ID: \d+\)\s+#', '', $value);
                    },
                    'choice_value' => function ($key) {
                        return $key;
                    },
                   // 'choices_as_values' => true
                )
            )
            ->add('node', ChoiceType::class, array(
                    'choices'  => array(
                        'All nodes' => 'ALL'
                    ),
                    'attr' => array(
                        'class' => 'form-control input-sm'
                    ),
                    'placeholder' => false,
                    'required' => false,
                    'choice_label' => function ($key, $value) {
                        return preg_replace('#^\(ID: \d+\)\s+#', '', $value);
                    },
                    'choice_value' => function ($key) {
                        return $key;
                    },
                    //'choices_as_values' => true
                )
            )
            ->add('vo', ChoiceType::class, array(
                    'choices'  => $this->vo,
                    'attr' => array(
                        'class' => 'form-control input-sm'
                    ),
                    'placeholder' => false,
                    'required' => false,
                    'choice_label' => function ($key, $value) {
                        return preg_replace('#^\(ID: \d+\)\s+#', '', $value);
                    },
                    'choice_value' => function ($key) {
                        return $key;
                    },
                    //'choices_as_values' => true,
                )
            )
            ->add('adding',CheckboxType::class, array(
                    'label'    => 'Get a notification when a downtime is added',
                    'required' => false,
                )
            )
            ->add('beginning',CheckboxType::class, array(
                    'label'    => 'Get a notification when a downtime is started',
                    'required' => false,
                )
            )
            ->add('ending',CheckboxType::class, array(
                    'label'    => 'Get a notification when a downtime is ended',
                    'required' => false,
                )
            )
            ->add('communications', CollectionType::class, array(
                'entry_type' => 'AppBundle\Form\Downtime\CommunicationType',
                'label' => false,

                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,

                'prototype' => true,
                'prototype_name' => '_namec_',
                'entry_options' => array(
                    'label' => false,
                ),
            ))
        ;

        $builder->get('site')->resetViewTransformers();
        $builder->get('node')->resetViewTransformers();
    }


    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Downtime\Subscription'
        ));
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'appbundle_subscription';
    }
}
