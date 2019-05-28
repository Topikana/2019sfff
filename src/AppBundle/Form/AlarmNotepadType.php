<?php
/**
 * Created by PhpStorm.
 * User: letellie
 * Date: 13/09/18
 * Time: 16:23
 */

namespace AppBundle\Form;


use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class AlarmNotepadType  extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id_alarm', EntityType::class, array('property' => 'name',
                'class' => 'A'

                ));
    }


}