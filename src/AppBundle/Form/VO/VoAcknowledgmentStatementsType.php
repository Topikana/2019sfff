<?php

namespace AppBundle\Form\VO;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VoAcknowledgmentStatementsType extends AbstractType
{


    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
//        $choices = array(
//            "Don't use." => 0,
//            "Use an Acknowledgment Statement" => 1);

        $builder
//            ->add('as_need', ChoiceType::class, array(
//                'expanded' => true,
//                'multiple' => false,
//                'choices' => $choices,
//                'label' => '',
//                'mapped' => false,
//                'data' => 0
//            ))
            ->add('grantid', TextType::class, array(
                'attr' => array(
                    'class' => 'form-control input-sm',
                    'help' => 'To acknowledge EGI and the project<br><br>
                               This work used the <strong>EGI Infrastructure</strong> and is co-funded by the <strong>EGI-Engage project (Horizon 2020)</strong> under Grant number <strong>654142</strong>.<br><br>
                               To acknowledge EGI, the project and specific countries providing resources.<br><br>
                               This work used the <strong>EGI Infrastructure</strong> through resources from Country_1, Country_2, … and is co-funded by the <strong>EGI-Engage project (Horizon 2020)</strong>
                                under Grant number <strong>654142</strong>.<br><br>
                                Use comma to set several grant ID.'
                ),
                'label' => "Grant ID (if applies)",
                'required' => false
            ))
            ->add('publicationUrl', UrlType::class, array(
                'attr' => array(
                    'class' => 'form-control input-sm',
                    'help' => 'URL to scientific list publications from this VO.'
                ),
                'label' => "Scientific publications URL",
                'required' => false
            ))
            ->add('suggested', TextareaType::class, array(
                'attr' => array(
                    'class' => 'form-control input-sm'
                ),
                'label' => "Suggested Acknowledgment",
                'required' => true
            ))
            ->add('use_relationShip', CheckboxType::class,array(
                'attr' => array(
                    'help' => 'You can specify a more generic statement and clarify which relationships
                we can extract (e.g., using EGI, using NGI:Country_Code, related to VO:VONAME)'
                ),
                'required' => false,
                'mapped' => false
            ))
            ->add('relationShip', TextareaType::class, array(
                'attr' => array(
                    'class' => 'form-control input-sm'
                ),
                'label' => "Relationship to be extracted",
                'required' => false
            ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\VO\VoAcknowledgmentStatements'
        ));
    }


}
