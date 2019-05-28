<?php

namespace AppBundle\Form\VO;

use AppBundle\Entity\VO\VoHeader;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VoType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('serial', HiddenType::class, array(
                'mapped' => false
            ))
            ->add('saveVo', SubmitType::class, array(
                'label' => "Save",
                'attr' => array(
                    'class' => "btn btn-lg btn-success",
                )
            ));

    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\VO\Vo'
        ));
    }
}
