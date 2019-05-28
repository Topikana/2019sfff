<?php

namespace AppBundle\Form\Broadcast;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;



/**
 * Created by PhpStorm.
 * User: lsouai
 * Date: 09/02/16
 * Time: 14:43
 */
class BroadcastMessageType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $pubChoices = array(
            "Broadcast+Archives" => 0,
            "Broadcast+Archives+News" => 1,
            "Broadcast" => 2,
            "Model" => 3);


        $builder
            ->add('publication_type', ChoiceType::class, array(
                'expanded' => false,
                'multiple' => false,
                'choices' => $pubChoices,
                'choice_label' => function ($key, $value) {
                    return preg_replace('#^\(ID: \d+\)\s+#', '', $value);
                },
                'choice_value' => function ($key) {
                    return $key;
                },
              //  'choices_as_values' => true,
                'label' => '',
//                'data' => 0,
                "attr" => array("help" => "<ul>
          <br/><li><strong> Broadcast+Archives+News :</strong> the broadcast will be sent, visible in the archive, and the news published in the homepage</li>
          <br/><li><strong> Broadcast+Archives :</strong> the broadcast will be sent and visible in the archive</li>
          <br/><li><strong> Broadcast:</strong> the broadcast will be sent and visible only by the author of the boradcast.</li></ul>")
            ))
            ->add('confirmation', CheckboxType::class, array(

                "label" => "send me a confirmation",
                "required" => false,
                'mapped' => false,
                "data" => true,


            ))
            ->add('author_cn', TextType::class, array(
                  'attr' => array(
                    'class' => 'form-control input-sm',
                      'readonly' => ''
                  ),
                'label' => "Your name",
            ))
            ->add('author_email', EmailType::class, array(
                'attr' => array(
                    'class' => 'form-control input-sm',
                    'placeholder' => 'Enter your mail'
                ),
                "label" => "Your email"
            ))
            ->add('cc', EmailType::class, array(
                'attr' => array(
                    'class' => 'form-control input-sm',
                    'placeholder' => 'Use comma(,) as e-mail address separator',
                    'multiple' => '' ,
                    'help' => '<p>Use comma(,) as e-mail address separator</p>',
                ),
                "label" => "Cc",
                'required' => false
            ))
            ->add('subject', TextType::class, array(
                'attr' => array(
                    'class' => 'form-control input-sm',
                    'minlength' => 10,
                    'title' => 'The subject of your mail must have at least 10 characters'),

                'label' => "News title / mail subject",
            ))
            ->add('body', TextareaType::class, array(
                'attr' => array(
                    'class' => 'form-control input-sm',
                    'minlength'     => 50,
                    "title" => "The body of your mail must have at least 50 characters",
                    'onkeyup' => 'validateTextarea(this)'
                ),
                'label' => "News content / mail body",
            ))
            ->add('targets', HiddenType::class, array(
                'mapped' => false
                ));
//            ->add('send', SubmitType::class, array(
//                "attr" => array('class' => 'pull-right btn btn-primary'),
//                'label' => 'Send message'));


    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Broadcast\BroadcastMessage'
        ));
    }
}