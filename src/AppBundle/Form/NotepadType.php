<?php

namespace AppBundle\Form;


use Doctrine\DBAL\Types\TimeType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class NotepadType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('creationDate', DateType::class, array(
                'widget' => 'single_text',
                'format' => "MM/dd/yyyy HH:mm:ss",
                'data' => new \DateTime("now"),
                'label' => false,
                'attr' => array('style'=>'display:none;')
            ))
            ->add('lastUpdate', DateType::class, array(
                'widget' => 'single_text',
                'format' => "MM/dd/yyyy HH:mm",
                'data' => new \DateTime("now"),
                'attr' => array('style'=>'display:none;'),
                'label' => false,
            ))
            ->add('subject', TextType::class)
            ->add('carbonCopy',ChoiceType::class,[
                'choices'  => [
                    'site' => 0,
                    'ngi' => 1,
                    'rod' => 2,
                ],
                'multiple' => true,
                'expanded' =>true
            ])
            ->add('site', TextType::class)
            ->add('comment', TextareaType::class)
//            ->add('status', ChoiceType::class, [
//                'choices' => [
//                    'open' => 0,
//                    'close' => 1,
//                ],
//                'multiple' => false,
//                'expanded' => true,
//            ])
            ->add('group_alarms', TextType::class, array(
                'attr' => array('style'=>'display:none;'),
                'label' => false,
                'required' => false,
            ));
//            ->add('lastModifer', TextType::class, array (
//                'attr' => array('style'=>'display:none;')
//            ));
        }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Notepad'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_notepad';
    }


}
