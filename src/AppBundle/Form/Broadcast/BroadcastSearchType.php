<?php

namespace AppBundle\Form\Broadcast;

use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

/**
 * Created by PhpStorm.
 * User: lsouai
 * Date: 09/02/16
 * Time: 14:43
 */
class BroadcastSearchType extends AbstractType
{

    const START_YEAR = 2013;


    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $years = range(static::START_YEAR, date("Y"));

        $builder
            ->add('begin_date', DateType::class, array(
                "input" => "datetime",
                'widget' => 'choice',
                'years' => array_combine($years, $years),
//                "data" => self::START_YEAR."/01/01"

            ))
            ->add('end_date', DateType::class, array(
                "input" => "datetime",
                'widget' => 'choice',
                'years' => array_combine($years, $years),
                "data" => new \DateTime()

            ))
            ->add('author', TextType::class, array(
                'attr' => array(
                    'class' => 'form-control input-sm',
                    'pattern'     => '.{5,}',
                    "title" => "The author must have at least 5 characters",
                ),
                'label' => "Author",
                'required' => false
            ))
            ->add('subject', TextType::class, array(
                'attr' => array(
                    'class' => 'form-control input-sm',
                    'pattern'     => '.{5,}',
                    "title" => "The subject must have at least 5 characters"),

                'required' => false,
                'label' => "Subject / Description"
            ))
            ->add('body', TextType::class, array(
                'attr' => array(
                    'class' => 'form-control input-sm',
                    'pattern'     => '.{5,}',
                    "title" => "The body must have at least 5 characters",
                    'onkeyup' => 'validateTextarea(this)'

                ),
                'required' => false,
                'label' => "Text in the body",
            ))
            ->add('email', TextType::class, array(
                'attr' => array(
                    'class' => 'form-control input-sm',
                ),
                'required' => false,
                'label' => "Contact email",
            ))
            ->add('send', SubmitType::class, array(
                "attr" => array('class' => 'btn btn-primary'),
                'label' => 'Search'));



    }

//    /**
//     * @param OptionsResolver $resolver
//     */
//    public function configureOptions(OptionsResolver $resolver)
//    {
//        $resolver->setDefaults(array(
//            'data_class' => 'AppBundle\Entity\Broadcast\BroadcastMessage'
//        ));
//    }
}