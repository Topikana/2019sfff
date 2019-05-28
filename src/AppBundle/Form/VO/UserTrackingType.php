<?php
/**
 * Created by PhpStorm.
 * User: lsouai
 * Date: 25/02/16
 * Time: 09:43
 */

namespace AppBundle\Form\VO;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class UserTrackingType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('DN', TextType::class, array(
                "label" => "DN"
            ))

            ->add('vo', TextType::class, array(
                'attr' => array(
                    "readonly" => ''
                ),
                "label" => "Vo"
            ))
            ->add('name', TextType::class, array(
                'attr' => array(
                    "readonly" => ''
                ),
                "label" => "Your name",
            ))
            ->add('email', EmailType::class, array(
                'attr' => array(
                    "disabled" => 'disabled',
                    'title' => 'Please enter tour email',
                ),
                "label" => "Your email"
            ))

            ->add('subject', TextType::class, array(
                'attr' => array(
                    'pattern' => '.{5,}',
                    'title' => 'The subject of your mail must have at least 5 characters',
                    "disabled" => 'disabled'
                ),
                'label' => "Subject",
            ))
            ->add('body', TextareaType::class, array(
                'attr' => array(
                    'pattern'     => '.{20,}',
                    "title" => "The body of your mail must have at least 20 characters",
                    'onkeyup' => 'validateTextarea(this)',
                    "disabled" => 'disabled'
                ),
            ))

            ->add('send', SubmitType::class, array(
                "attr" => array('class' => 'btn btn-sm btn-primary',
                    "disabled" => 'disabled'),

                'label' => 'Send Email'));

    }


}