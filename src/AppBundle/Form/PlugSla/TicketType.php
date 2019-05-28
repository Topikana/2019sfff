<?php
/**
 * Created by PhpStorm.
 * User: letellie
 * Date: 06/07/18
 * Time: 11:21
 */

namespace AppBundle\Form\PlugSla;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Yaml\Parser;

class TicketType extends AbstractType
{





    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $yaml = new Parser();
        $jiraItems = $yaml->parse(file_get_contents(__DIR__ . '/../../../../app/config/Plugsla/jiraItems.yml'));


        foreach ($jiraItems["parameters"]["jiraItems"] as $key => $item)
        {

            if ($item['visible']==true) {

                if (isset($item['values']))
                {
                    $builder->add($key, ChoiceType::class,['choices'  => $item["values"],'required' => false]);
                }
                else {
                    if ($item['required'] == true)
                        $builder->add($key, TextType::class);
                    else
                        $builder->add($key, TextType::class, array('required' => false));
                }

            }
        }

        $builder
            ->add('id', TextType::class,  array(
                'attr' => array(
                    'readonly' => true,
                ),
            ))
            ->add('dateCreated', DateTimeType::class,array('date_widget' => 'single_text', 'time_widget' => 'single_text'))
            ->add('dateUpdated', DateTimeType::class,array('date_widget' => 'single_text', 'time_widget' => 'single_text'))
            ->add('submit', SubmitType::class, array(
                'attr' => ['class' => 'btn-sm btn btn-primary fa fa-save'],
                'label' => 'Save'
            ))

            ->getForm();
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(

        ));
    }
}