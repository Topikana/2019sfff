<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SettingsType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('infrastructure', ChoiceType::class,[
                'choices' => [
                    'production' => 'prod',
                    'all' => 'all'
                ],

            ])
            ->add('certifiedStatus', ChoiceType::class,[
                'choices'  => [
                    'certified' => 'Certified',
                    'all' => 'all',

                ],
            ])
            ->add('alarmStatus', ChoiceType::class,[
                'choices'  => [
                    'Ok' => 0,
                    'Warning' => 1,
                    'Critical' => 2,
                    'Unknown' => 3,
                    'Assigned' => 4,
                    ],
                    'multiple' => true,
                    'expanded' =>true,
                    'choice_attr' => function($val, $key, $index) {
                        if ($val == 2){
                            return [
                                'class' => 'checkbox-inline',
                                'checked' => 'checked'
                            ];
                        }
                        // adds a class like attending_yes, attending_no, etc
                        return [
                            'class' => 'checkbox-inline',
                            ];
                    }
                ]
            )
            ->add('filter_columns', ChoiceType::class,[
                'choices' =>[
                    'Nb Alarms critical' => 1,
                    'Nb Alarms warning' => 2,
                    'Nb Tickets' => 3,
                ],
                'choice_attr' => function($val, $key, $index) {
                    if ($val == 1){
                        return [
                            'checked' => 'checked'
                        ];
                    }
                    return [];
                },
                'expanded' =>true
            ])
            ->add('profile_alarm', ChoiceType::class,[
                    'choices'  => [
                        'OPS_MONITOR' => 'OPS_MONITOR',
                        'ARGO_MON_OPERATORS' => 'ARGO_MON_OPERATORS',
                        'ARGO_MON_CRITICAL' => 'ARGO_MON_CRITICAL',
                        'MW_MONITOR' => 'MW_MONITOR',
                        'OPS_MONITOR_CRITICAL' => 'OPS_MONITOR_CRITICAL',
                        'ARGO_MON' => 'ARGO_MON',
                    ],
                    'multiple' => true,
                    'expanded' =>true,
                    'choice_attr' => function($val, $key, $index) {
                        if ($val == 'OPS_MONITOR' || $val == 'ARGO_MON_OPERATORS' || $val == 'MW_MONITOR' ){
                            return [
                                'class' => 'checkbox-inline',
                                'checked' => 'checked'
                            ];
                        }
                        // adds a class like attending_yes, attending_no, etc
                        return [
                            'class' => 'checkbox-inline',
                        ];
                    }
                ]
            )
        ;
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Settings'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_settings';
    }


}
