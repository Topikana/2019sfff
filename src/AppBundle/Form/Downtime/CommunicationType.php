<?php

namespace AppBundle\Form\Downtime;

use AppBundle\Entity\Downtime\Communication;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class CommunicationType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type', ChoiceType::class, array(
                'attr' => array(
                    'class' => 'form-control input-sm',
                    'onchange'=> 'changeChannel(this)'
                ),
                'choices' => array(
                    'Email (HTML)' => Communication::TYPE_EMAIL_HTML,
                    'Email (text)' => Communication::TYPE_EMAIL_TEXT,
                    'RSS' => Communication::TYPE_RSS,
                    'ICAL' => Communication::TYPE_ICAL
                ),
                'choice_label' => function ($key, $value) {
                    return preg_replace('#^\(ID: \d+\)\s+#', '', $value);
                },
                'choice_value' => function ($key) {
                    return $key;
                },
               // 'choices_as_values' => true,
                'expanded' => false,
                'multiple' => false
            ))
            ->add('value', TextType::class, array(
                'attr' => array(
                    'class' => 'form-control input-sm'
                ),
                'required' => true
            ))
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Downtime\Communication'
        ));
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'appbundle_communication';
    }
}
