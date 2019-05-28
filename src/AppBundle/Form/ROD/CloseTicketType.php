<?php

namespace AppBundle\Form\ROD;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CloseTicketType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('solution', TextareaType::class,[])
            ->add('ticketId', HiddenType::class)
            ->add('site', HiddenType::class)
            ->add('addVerifyStatusInGGUS',ChoiceType::class,[
                'choices' =>[
                    'yes' => 1,
                    'no' => 0,
                ],

                'choice_attr' => function($val, $key, $index) {
                    if ($val == 1){
                        return [
                            'checked' => 'checked'
                        ];
                    }
                    return [];
                },
                'expanded' =>true,
                'attr' => [
                    'class' => 'd-flex'
                ]
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\ROD\Ticket'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_rod_close_ticket';
    }


}
