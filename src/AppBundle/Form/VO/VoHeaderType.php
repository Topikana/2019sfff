<?php

namespace AppBundle\Form\VO;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class VoHeaderType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, array(
                'attr' => array(
                    'class' => 'form-control input-sm',
                    'help' => '<strong>Vo name</strong>

Each virtual organization must have a unique name.<br>
The name must conform to DNS naming conventions and contain only lower-case letters.
Names within the egi.eu domain are controlled by the  EGI User Community Support Team Representatives.
Other domains may be used, but you must have the right to allocate (or obtain) names within that domain.
<br>
Minimal restrictions are:
<br>
- VO name uniqueness will be enforced through technical implementation.<br>
- VO name should be using alphanumerical characters plus the following characters <mark><strong> - </strong></mark> and <mark><strong> . </mark></strong><br>
- VO name <b>MUST</b> be lowercase<br>
- VO name should not be an offending word in any language.<br>
- VO name should not be just a sequence of characters. Especially, names like <mark><strong> aaa </mark></strong> will be refused.
'
                ),
                'label' => "Name",
                'required' => true
            ))
            ->add('alias', TextType::class, array(
                'attr' => array(
                    'class' => 'form-control input-sm',
                    'help' => 'Short Name of the VO.'
                ),
                'required' => false
            ))
            ->add("serial", TextType::class, array(
                'attr' => array(
                    'class' => 'form-control input-sm',
                    'help' => 'Visible only in the update phase - Internal ID used to update VO ID cards'
                ),
                'required' => false
            ))
            ->add("validation_date", DateType::class, array(
                'attr' => array(
                    'class' => 'form-control input-sm'
                ),
                'label' => "Validation Date",
                'required' => false,
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd'
            ))
            ->add('enrollment_url', UrlType::class, array(
                'attr' => array(
                    'class' => 'form-control input-sm',
                    'help' => 'The URL which points to the interface used to enroll new members into the virtual organization.<br>
                    For some VOs, this may just be an informational page describing the procedure to request access for that VO.'
                ),
                'required' => false
            ))
            ->add('homepage_url', UrlType::class, array(
                'attr' => array(
                    'class' => 'form-control input-sm',
                    'help' => 'URL of a homepage where background information about the virtual organization can be found. <br>
                    (For example about the scientific goals of the community and how the EGI VO helps the community to achieve these goals.)'
                ),
            ))
            ->add('support_procedure_url', UrlType::class, array(
                'attr' => array(
                    'class' => 'form-control input-sm',
                    'help' => 'The support procedure URL where is described how support is provided for this VO and how users should
                    communicate in order to report an issue or request assistance.'
                ),
                'required' => false
            ))
            ->add('description', TextareaType::class, array(
                'attr' => array(
                    'class' => 'form-control input-sm',
                    'pattern'     => '.{30,}',
                    "help" => "The description must have at least 30 characters",
                    'onkeyup' => 'validateTextarea(this)'
                ),
            ))
            ->add('arc_supported', CheckboxType::class, array(
                'required' => false,
                'attr' => array(
                    'help' => 'The Advanced Resource Connector (ARC) middleware integrates computing resources
                    (usually, computing clusters managed by a batch system or standalone workstations) and storage facilities,
                    making them available via a secure common Grid layer. Complete documentation, including component descriptions,
                    installation details, usage instructions, articles and presentations, can be found at the documentation page. '
                ),
            ))
            ->add('glite_supported', CheckboxType::class, array(
                'required' => false,
                'attr' => array(
                    'help' => 'The gLite distribution is an integrated set of components designed to enable resource sharing.
                    In other words, this is middleware for building a grid.<br>
                    The gLite middleware was produced by the EGI project and it is currently being developed by the EMI project.
                    In addition to code developed within the project, the gLite distribution pulls together contributions
                    from many other projects, including LCG and VDT. The distribution model is to construct different services
                    (node-types) from these components and then ensure easy installation and configuration on the
                    chosen platforms (currently Scientific Linux versions 4 and 5, and also Debian 4 for the WNs).<br>
                    gLite middleware is currently deployed on hundreds of sites of different DCIs and enables global science
                    in a number of disciplines, notably serving the LCG project.'
                )
            ))
            ->add('globus_supported', CheckboxType::class, array(
                'required' => false,
                'attr' => array(
                    'help' => 'Globus Online is a high-performance, reliable, secure data movement service.<br>
                    Globus Online makes robust file transfer capabilities, traditionally available only on expensive,
                    special-purpose software systems, accessible to everyone.<br>
                    Globus Online is a hosted service in the cloud that you can use today without building any custom IT infrastructure.'
                ),
            ))
            ->add('unicore_supported', CheckboxType::class, array(
                'required' => false,
                'attr' => array(
                    'help' => 'UNICORE (Uniform Interface to Computing Resources) offers a ready-to-run Grid system including
                    client and server software. UNICORE makes distributed computing and data resources available in a seamless
                    and secure way in intranets and the internet.'
                )
            ))
            ->add('cloud_computing_supported', CheckboxType::class, array(
                'required' => false,
                'attr' => array(
                    'help' => 'If the VO wants to use or is using Cloud Resources for computations.<br>
                    More details about the cloud infrastructure is given on the following location.'
                )
            ))
            ->add('cloud_storage_supported', CheckboxType::class, array(
                'required' => false,
                'attr' => array(
                    'help' => 'If the VO wants to use or is using Cloud Resources to store data.<br>
                    More details about the cloud infrastructure is given on the following location . '
                )
            ))
            ->add('desktop_grid_supported', CheckboxType::class, array(
                'required' => false
            ))
            ->add('vo_scope', EntityType::class, array(
                'class' => 'AppBundle\Entity\VO\VoScope',
                'choice_label' => 'scope',
                'attr' => array(
                    'class' => 'form-control input-sm',
                    'onchange' => '$("#vo_VoHeader_scope_id").val($(this).val())',
                    'help' => '<strong>Official scope values</strong>
<br><u>GLOBAL</u>: the VO is supported by sites from multiple countries and all of these countries are represented by its National Grid Infrastructure (NGI); comprises an international user community and/or has international resources coming from sites of different countries represented by their National Grid Infrastructures (NGIs).
<br><u>NATIONAL</u>: the VO is supported by sites that belong to one country which is represented by its National Grid Infrastructure (NGI); The supporting NGI is indicated in the scope of the VO, like for example NGI-Italy or NGI-France. Members of the VO might come from or outside of the NGI depending on the Acceptable Use Policy of the VO.
<br>Note that the new definitions specify the Scope based on the location of the sites. The members of the VO can come from anywhere as long as the AUP allows this. This concept would allow the usage of national VOs by foreign users and would encourage international collaborations without needing to change VO registration in the Operations Portal.
'
                ),
                'label' => "Scope",
                'mapped' => false,
                'data' => $builder->getData()->getScopeId()
            ))
            ->add('scope_id', HiddenType::class, array(
                'required' => false,
                'data' => $builder->getData()->getScopeId()
            ))
            ->add('notify_sites', CheckboxType::class, array(
                'required' => false,
                'attr' => array(
                    'help' => 'Having dedicated GGUS support implies that your VO commits itself to provide people
                    dedicated user support that can be contacted by GGUS and via GGUS. <br>
                    You will have to fill up the FAQ of your support unit and provide for GGUS support unit the document.<br>
                    Please follow the instructions given at https://wiki.egi.eu/wiki/FAQ_GGUS-New-Support-Unit.'
                )
            ))
            ->add('aup_type', ChoiceType::class, array(
                'attr' => array(
                    'class' => 'form-control input-sm',
                    'onchange' => 'filterAupDisplay(this)',
                    'help' => 'AUP types :<br/>
                        <ul><li><b>url</b> : store a link to a distant AUP description</li>
                        <li><b>text </b>: store text description in our database</li>
                        <li><b>file </b>: upload a file on our server</li></ul>'
                ),
                'choices' => array(
                    'Text' => "Text",
                    'Url' => "Url",
                    'File' => "File"
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
            ->add('aup', HiddenType::class, array(
                'required' => false
            ))
            ->add('aupText', TextareaType::class, array(
                'attr' => array(
                    'class' => 'form-control input-sm',
                    'pattern'     => '.{30,}',
                    "title" => "The Aup text must have at least 30 characters",
                                  ),
                'mapped' => false,

            ))
            ->add('aupUrl', UrlType::class, array(
                'attr' => array(
                    'class' => 'form-control input-sm'
                ),
                'mapped' => false
            ))
            ->add('user', HiddenType::class, array(
                'required' => false
            ))
        ;

    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\VO\VoHeader'
        ));
    }
}
