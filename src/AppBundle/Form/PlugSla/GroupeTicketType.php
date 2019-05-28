<?php

namespace AppBundle\Form\PlugSla;

use AppBundle\Entity\PlugSla\GroupeTicket;
use AppBundle\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GroupeTicketType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('DN_authorized', EntityType::Class, [
                'attr' => ['class'=>'u-full-width'],
                'required' => true,
                'class'=>'AppBundle:User',
                'choice_label' => function (User $customer) {
                if(strstr($customer->getDn(), 'Id=')){
                    $id =  explode('Id=',$customer->getDn());
                    return $customer->getUsername() . ' ' . $id[1];
                }else{
                    return $customer->getUsername() . ' ' . $customer->getDn();
                }
                },
                'choice_value'=>'dn',
                'label'=>'Username and Dn',
            ])
            ->add('type')
            ->add('submit', SubmitType::class,[
                'attr' => ['class' => 'btn-sm btn btn-primary mt-4']
            ]);
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => GroupeTicket::class, User::class,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_plugsla_groupeticket';
    }


}
