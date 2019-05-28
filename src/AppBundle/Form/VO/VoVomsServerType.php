<?php

namespace AppBundle\Form\VO;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VoVomsServerType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('serial', HiddenType::class)
            ->add('members_list_url', UrlType::class, array(
                'attr' => array(
                    'class' => 'form-control input-sm',
                ),
                'label' => 'Url for "listMembers" method',
                'required' => true
            ))
            ->add('https_port', TextType::class, array(
                'attr' => array(
                    'class' => 'form-control input-sm',
                    'help' => 'Port used to access to the VOMS server using https protocol. Default value is usually <b>8443</b>.'
                ),
                'label' => "Https port",
                'required' => true
            ))
            ->add('vomses_port', TextType::class, array(
                'attr' => array(
                    'class' => 'form-control input-sm',
                    'help' => 'Port used to access to the VOMS server using vomses protocol. Default value is usually <b>15002</b>.'
                ),
                'label' => "Vomses port",
                'required' => true
            ))
            ->add('is_vomsadmin_server', CheckboxType::class, array(
                'label' => "Is Vomsadmin server",
                'required' => false,
                'attr' => array(
                    'help' => 'This option indicates if the associated VOMS server should be used to generate gridmapfiles for the VO,
            or if this is a replica/backup/additionnal/test server, not to be used for configuration files.<br><br>
            <strong>1.</strong> All VOMS server listed in the Operations Portal for a particular VO are needed for supporting
            that VO --> VOMS-aware services for that VO need to have the corresponding LSC files in the <mark><strong>vomsdir</strong></mark> for that
            VO and possibly the corresponding <mark><strong> vomses </strong></mark> configuration for generating proxies, depending on the type of service.<br><br>
            <strong>2.</strong> Only VOMS servers listed with the Admin server flag should be used for making grid-mapfiles.'
                )
            ));

    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\VO\VoVomsServer'
        ));
    }


}
