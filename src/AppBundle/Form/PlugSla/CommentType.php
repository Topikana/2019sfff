<?php
/**
 * Created by PhpStorm.
 * User: letellie
 * Date: 01/08/18
 * Time: 16:34
 */

namespace AppBundle\Form\PlugSla;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('body', TextareaType::class)
            ->add('submit', SubmitType::class, array(
                'attr' => ['class' => 'btn btn-primary'
                ]
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([

            ]);
    }
}