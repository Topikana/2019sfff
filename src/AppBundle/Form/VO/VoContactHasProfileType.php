<?php
/**
 * Created by PhpStorm.
 * User: frebault
 * Date: 10/02/16
 * Time: 17:02
 */

namespace AppBundle\Form\VO;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class VoContactHasProfileType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('serial', HiddenType::class)
            ->add('comment', TextType::class, array(
                'attr' => array(
                    'class' => 'form-control input-sm',
                ),
                'label'=> 'Comment',
                'required' => false
            ))
            ->add('user_profile_id',HiddenType::class)
            ->add('VoUserProfile', EntityType::class, array(
                'class' => 'AppBundle\Entity\VO\VoUserProfile',
                'choice_label' => 'profile',
                'attr' => array(
                    'class' => 'form-control input-sm',
                    'onChange' => '$("#vo_contact_has_profile_user_profile_id").val($(this).val())'
                ),
                'mapped' => false
            ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\VO\VoContactHasProfile'
        ));
    }
}