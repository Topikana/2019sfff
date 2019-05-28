<?php

namespace AppBundle\Form\VO;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;


class VoMailingListType extends AbstractType
{


    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('admins_mailing_list', EmailType::class, array(
                    'attr' => array(
                        'class' => 'form-control input-sm',
                        'help' => 'The generic contact used to reach VO managers and administrators e.g through the EGI BROADCAST tool '
                    ),
                    'label' => "VO Managers (mailing list)",
                    'required' => true
                ))
            ->add('operations_mailing_list', EmailType::class, array(
                'attr' => array(
                    'class' => 'form-control input-sm',
                    'help' => 'Generic contact used for contacting the VO about operational and technical issues or announcement.'
                ),
                'label' => "Operations (mailing list)",
                'required' => false
            ))
            ->add('user_support_mailing_list', EmailType::class, array(
                'attr' => array(
                    'class' => 'form-control input-sm',
                    'help' => 'The contact for this VO regarding GGUS tickets which have been identified to be VO-specific problems.<br>
                                                If no value is given, then this contact will default to the VO managers.'
                ),
                'label' => "User Support (mailing list)",
                'required' => false
            ))
            ->add('users_mailing_list', EmailType::class, array(
                'attr' => array(
                    'class' => 'form-control input-sm',
                    'help' => 'The generic contact used to reach VO users e.g  through the EGI BROADCAST tool'
                ),
                'label' => "VO Users (mailing list)",
                'required' => true
            ))
            ->add('security_contact_mailing_list', EmailType::class, array(
                'attr' => array(
                    'class' => 'form-control input-sm',
                    'multiple' => '' ,
                    'help' => 'Use coma to set several addresses.'
                ),
                'label' => "Security (mailing list, multiple)",
                'required' => true
            ))
            ->add('notify_sites', CheckboxType::class, array(
                'required' => false
            ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\VO\VoMailingList'
        ));
    }



}
