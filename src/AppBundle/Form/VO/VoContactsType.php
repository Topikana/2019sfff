<?php

namespace AppBundle\Form\VO;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VoContactsType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('first_name', TextType::class, array(
                'attr' => array(
                    'class' => 'form-control input-sm',
                ),
                'label' => "First Name",
                'required' => true
            ))
            ->add('last_name', TextType::class, array(
                'attr' => array(
                    'class' => 'form-control input-sm'
                ),
                'label'=> 'Last Name',
                'required' => true
            ))
            ->add('dn', TextType::class, array(
                'attr' => array(
                    'class' => 'form-control input-sm',
                    'onChange' => '$("#contactDn").val($(this).val())'
                ),
                'label'=> 'Dn',
                'required' => true
            ))
            ->add('email', EmailType::class, array(
                'attr' => array(
                    'class' => 'form-control input-sm',
                ),
                'label'=> 'Email',
                'required' => true
            ))
            ->add('grid_body', HiddenType::class);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\VO\VoContacts'
        ));
    }


}
