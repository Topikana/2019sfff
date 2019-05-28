<?php
/**
 * Created by PhpStorm.
 * User: frebault
 * Date: 04/03/16
 * Time: 09:35
 */

namespace AppBundle\Form\VO;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ManageAupFileType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('aupFile', FileType::class, array(
                'label' => 'Select a file'
            ))
            ->add('name', TextType::class, array(
                'attr' => array(
                    "readonly" => '',
                    'class' => 'form-control input-sm',
                ),
                "label" => "AUP file name"
            ));

        return $builder;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\VO\AupFile'
        ));
    }

}