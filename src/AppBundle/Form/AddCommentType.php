<?php
/**
 * Created by PhpStorm.
 * User: letellie
 * Date: 24/09/18
 * Time: 16:44
 */

namespace AppBundle\Form;



use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddCommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('commentary', TextareaType::class, array(
                'attr' => array('class' => 'form-control',
                                'rows' => '3'
                )))
            ->add('creationDate', DateType::class, array(
                'widget' => 'single_text',
                'format' => "MM/dd/yyyy HH:mm:ss",
                'data' => new \DateTime("now"),
                'label' => false,
                'attr' => array('style'=>'display:none;')
            ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Comment'
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