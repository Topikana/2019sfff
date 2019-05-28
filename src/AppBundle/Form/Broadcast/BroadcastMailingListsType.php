<?php

namespace AppBundle\Form\Broadcast;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;



/**
 * Created by PhpStorm.
 * User: lsouai
 * Date: 09/02/16
 * Time: 14:43
 */
class BroadcastMailingListsType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $scopeChoices = array(
            "private" => "private",
            "public" => "public");

        $builder
            ->add('scope', ChoiceType::class, array(
                'expanded' => false,
                'multiple' => false,
                'choices' => $scopeChoices,
                'choice_label' => function ($key, $value) {
                    return preg_replace('#^\(ID: \d+\)\s+#', '', $value);
                },
                'choice_value' => function ($key) {
                    return $key;
                },
              //  'choices_as_values' => true,
                'label' => '',
                'data' => "public",
            ))
            ->add('name', TextType::class, array(
                  'attr' => array(
                    'class' => 'form-control input-sm',
                  ),
                'label' => "Name",
            ))
            ->add('value', EmailType::class, array(
                'attr' => array(
                    'class' => 'form-control input-sm',
                ),
                "label" => "Email"
            ))

            ->add('save', SubmitType::class, array(
                "attr" => array('class' => 'pull-right btn btn-primary'),
                'label' => 'Save'));


    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Broadcast\BroadcastMailingLists'
        ));
    }
}