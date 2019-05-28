<?php
/**
 * Created by PhpStorm.
 * User: lsouai
 * Date: 03/12/15
 * Time: 14:23
 */

namespace AppBundle\Form\Home\Type;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MailType extends AbstractType
{


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, array(
                'attr' => array(
                    'placeholder' => 'Enter your name',
                ),

            ))
            ->add('email', EmailType::class, array(
                'attr' => array(
                    'placeholder' => 'Enter your mail'
                )
            ))
            ->add('cc', EmailType::class, array(
                'attr' => array(
                    'placeholder' => 'enter cc to copy mail',
                    'multiple' => '' ,
                    'help' => '<p>Use comma(,) as e-mail address separator</p>',
                ),
                'required' => false
            ))
            ->add('subject', TextType::class, array(
                'attr' => array(
                    'placeholder' => 'Enter the subject of your message',
                )
            ))
            ->add('body', TextareaType::class, array(
                'attr' => array(
                    'cols' => 90,
                    'rows' => 10,
                    'placeholder' => 'Insert your message here'
                )
            ))
            ->add('send', SubmitType::class, array(
                'label' => 'Send message','attr' => array('class' => 'btn-primary')
                ));
    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Home\Mail',
        ));
    }


    public function getName()
    {
        return 'mail';
    }
}
