<?php

namespace AppBundle\Form\VO;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VoRessourcesType extends AbstractType
{


    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('ram386', TextType::class, array(
                    'attr' => array(
                        'class' => 'form-control input-sm',
                        'help' => 'The virtual memory requirements per 32 bits server.<br>
                                If not specified, then you will have access to the default amount of virtual memory.<br>
                                This may vary between sites and machines.'
                    ),
                    'label' => "Max used physical non-swap i386 memory size (MB)",
                    'required' => false
                ))
            ->add('ram64', TextType::class, array(
                'attr' => array(
                    'class' => 'form-control input-sm',
                    'help' => 'The virtual memory requirements per 64 bits server.<br>
                                If not specified, then you will have access to the default amount of virtual memory.<br>
                                This may vary between sites and machines.'
                ),
                'label' => "Max used physical non-swap x86_64 memory size (MB)",
                'required' => false
            ))
            ->add('job_scratch_space', TextType::class, array(
                'attr' => array(
                    'class' => 'form-control input-sm',
                    'help' => 'The maximum scratch space needed by jobs in this VO given in MB.<br>
                            If not specified, then your jobs will have access to the default amount of scratch space.<br>
                            This limit may vary between sites.'
                ),
                'label' => "Max size of scratch space used by jobs (MB)",
                'required' => false
            ))
            ->add('job_max_cpu', TextType::class, array(
                'attr' => array(
                    'class' => 'form-control input-sm',
                    'help' => 'The CPU limit for this VO (in minutes on a machine rated at 1000 SpecInt2000).<br><br>
            A machine rated at 1000 SpecInt2000 is currently an average machine. <br>
            If you\'ve run an example job, find the SpecInt rating for your machine in the official list.<br>
            Multiply the time in minutes by (rating/1000) to determine the value for this field.<br><br>
            This is used for queue configuration at site\'s supporting this VO.<br>
            If this is not specified, then the limit will be determined by the site administrators and may vary between sites.'
                ),
                'label' => "Max time of job execution (minutes)",
                'required' => false
            ))
            ->add('job_max_wall', TextType::class, array(
                'attr' => array(
                    'class' => 'form-control input-sm',
                    'help' => 'The wallclock time limit for jobs in this VO. <br>
                    This is used for queue configuration at site\'s supporting this VO.<br>
                    If this is not specified, then the limit will be set by site administrator and may vary between sites.'
                ),
                'label' => "Job wall clock time limit (minutes)",
                'required' => false
            ))
            ->add('other_requirements', TextareaType::class, array(
                'attr' => array(
                    'class' => 'form-control input-sm',
                    'help' => 'Specific hardware requirements not defined in other fields, like RB, Fireman, RLS, required softwares, ...'
                ),
                'required' => false
            ))
            ->add('cpu_core', TextType::class, array(
                'attr' => array(
                    'class' => 'form-control input-sm'
                ),
                'label' => "CPU Core",
                'required' => false
            ))
            ->add('vm_ram', TextType::class, array(
                'attr' => array(
                    'class' => 'form-control input-sm'
                ),
                'label' => "VM Ram (MB)",
                'required' => false
            ))
            ->add('storage_size', TextType::class, array(
                'attr' => array(
                    'class' => 'form-control input-sm'
                ),
                'label' => "Storage Size (GB)",
                'required' => false
            ))
            ->add('public_ip')
            ->add('notify_sites', CheckboxType::class, array(
                'required' => false
            ))
        ->add('cvmfs', HiddenType::class, array(
            'attr' => array(
                'class' => 'form-control input-sm'
            ),
                'label' => "Endpoints (hostname + port)",
                'required' => false

        ))
            ->add('number_cores', TextType::class, array(
                'attr' => array(
                    'class' => 'form-control input-sm'
                ),
                'label' => "Minimum/preferred/maximum number of cores per job",
                'required' => false
            ))
            ->add('minimum_ram', TextType::class, array(
                'attr' => array(
                    'class' => 'form-control input-sm'
                ),
                'label' => "Minimum RAM values per job",
                'required' => false
            ))
            ->add('scratch_space_values', TextType::class, array(
                'attr' => array(
                    'class' => 'form-control input-sm'
                ),
                'label' => "Minimum scratch space values per job",
                'required' => false
            ));



    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\VO\VoRessources'
        ));
    }



}
