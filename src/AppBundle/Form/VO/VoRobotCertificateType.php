<?php

namespace AppBundle\Form\VO;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class VoRobotCertificateType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {



        $builder
            ->add('vo_name', TextType::class, array(
                'attr' => array(
                    'class' => 'form-control input-sm',
                ),
                "label" => "VO",
            ))
            ->add('email', EmailType::class, array(
                'attr' => array(
                    'class' => 'form-control input-sm',
                ),
                'label' => "Contact email",
            ))
            ->add('service_name', TextType::class, array(
                'attr' => array(
                    'class' => 'form-control input-sm',
                ),
                'label' => "Service Name",
            ))
            ->add('service_url', UrlType::class, array(
                'attr' => array(
                    'class' => 'form-control input-sm',
                ),
                'label' => "Service URL",

            ))
            ->add('robot_dn', TextType::class, array(
                'attr' => array(
                    'class' => 'form-control input-sm',
                ),
                'label' => "Robot DN",
            ))
            ->add('use_sub_proxies', CheckboxType::class, array(
                'attr' => array(
                    "onChange" => "showHideUseSubProxiesMessage(this)"
                ),
                'required' => false,
                'label' => "use per-user sub-proxies"
            ))
            ->add('id' ,HiddenType::class, array(
                'mapped' => false,
                "required" => false))
            ->add('save', SubmitType::class, array(
                "attr" => array('class' => 'btn btn-primary voAction'),
                'label' => 'Save Robot Certificate'));

    }




    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\VO\VoRobotCertificate',
            "voList" => ""
        ));
    }
}
