<?php
/**
 * Created by PhpStorm.
 * User: lsouai
 * Date: 14/03/16
 * Time: 15:52
 */

namespace AppBundle\Form\Metrics;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;

use AppBundle\Form\Metrics\IncompleteDateTransformer;

class MetricsReportType extends AbstractType
{

    const START_YEAR = 2013;


    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $years = range(static::START_YEAR, date("Y"));


        $entitiesChoices = array(
            'User metrics per VO' => 'vo',
            'User metrics per CA' => 'ca',
            'User metrics per Disciplines' => 'discipline',
            'Number of National, international VO' => 'national',
            'Vo Activities' => 'voActivities'
        );

        $builder
            ->add('begin_date', DateTimeType::class, array(
                'widget' => 'single_text',
                'label' => 'Start Date',
                'format' => 'MM/yyyy',
                'required' => false,
                'attr' => [
                    'class' => 'form-control input-inline datepicker',
                    'data-provide' => 'datepicker',
                    'data-date-format' => 'mm/yyyy',
                    'data-date-view-mode' => "years",
                    'data-date-min-view-mode'=>"months",
                    'data-date-end-date' => "0d",
                    "data-date-start-date" => "01/".self::START_YEAR
                ],
                'data' => new \DateTime(),
                'years' => array_combine($years, $years),
            ))
            ->add('start_date', DateTimeType::class, array(
                'widget' => 'single_text',
                'label' => 'Start Date',
                'format' => 'MM/yyyy',
                'required' => false,
                'attr' => [
                    'class' => 'form-control input-inline datepicker',
                    'data-provide' => 'datepicker',
                    'data-date-format' => 'mm/yyyy',
                    'data-date-view-mode' => "years",
                    'data-date-min-view-mode'=>"months",
                    'data-date-end-date' => "0d",
                    "data-date-start-date" => "01/".self::START_YEAR
                ],
                'data' => new \DateTime("-5 months"),
                'years' => array_combine($years, $years),
            ))
            ->add('end_date', DateTimeType::class, array(
                'label' => 'End Date',
                'widget' => 'single_text',
                'format' => 'MM/yyyy',
                'required' => false,
                'attr' => [
                    'class' => 'form-control input-inline datepicker',
                    'data-provide' => 'datepicker',
                    'data-date-format' => 'mm/yyyy',
                    'data-date-view-mode' => "years",
                    'data-date-min-view-mode'=>"months",
                    'data-date-end-date' => "0d",
                    "data-date-start-date" => "01/".self::START_YEAR
                ],
                'data' => new \DateTime(),
                'years' => array_combine($years, $years),
            ))
            ->add('entity', ChoiceType::class, array(
                'expanded' => true,
                'choices' => $entitiesChoices,
                'choice_label' => function ($key, $value) {
                    return preg_replace('#^\(ID: \d+\)\s+#', '', $value);
                },
                'choice_value' => function ($key) {
                    return $key;
                },
                //'choices_as_values' => true,
                'label' => '',
                'data' => 'vo',
            ))
            ->add('submit', SubmitType::class, array(
                "attr" => array('class' => 'btn btn-m btn-primary'),
                'label' => 'Submit'));
    }

}