<?php
/**
 * Created by PhpStorm.
 * User: letellie
 * Date: 02/04/19
 * Time: 14:11
 */

namespace AppBundle\Form\PlugSla;


use AppBundle\Entity\PlugSla\TypeTicket;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ServiceType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
       $builder->add('type', TextType::class, array(
           'required' => true,
           'label' => 'Service'
       ))
        ->add('submit', SubmitType::class, array(
            'attr' => ['class' => 'btn-sm btn btn-primary mt-4']
        ));

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
           'data_class' => TypeTicket::class
        ));
    }

}