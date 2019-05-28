<?php

namespace AppBundle\Form\VO;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


class VoVomsGroupType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $groupChoices = array('' => '',
            'Software Manager' => 'Software Manager',
            'Production Manager' => 'Production Manager',
            'Pilot' => 'Pilot',
            'Standard User' => 'Standard User',
            'Other' => 'Other'
        );

        asort($groupChoices);

        $ARChoices = array();
        for ($i = 0; $i <= 100; $i += 10) {
            $ARChoices[$i] = $i;
        }
        $builder
            ->add('serial', HiddenType::class)
            ->add('group_role', TextType::class, array(
                'attr' => array(
                    'class' => 'form-control input-sm',
                    'help' => '<strong>VOMS groups and roles</strong>
In VOMS terminology, a group is a subset of the VO containing members who share some responsibilites or
privileges in the project. Groups are organised hierarchically like a directory tree, starting from a VO-wide root group.<br>
A user can be a member of any number of groups, and a VOMS proxy contains the list of all groups the
user belongs to, but when the VOMS proxy is created the user can choose one of these groups as the <mark><strong>primary</strong></mark> group.<br>
Each group/role must have a description defining the rights associated with the group/role.If groups and/or roles are not needed for the VO, please leave this list empty.'
                ),
                'label' => "Group / role (FQAN)",
                'required' => true
            ))
            ->add('group_type', ChoiceType::class, array(
                'attr' => array(
                    'class' => 'form-control input-sm',
                ),
                'choices' => $groupChoices,
                'choice_label' => function ($key, $value) {
                    return preg_replace('#^\(ID: \d+\)\s+#', '', $value);
                },
                'choice_value' => function ($key) {
                    return $key;
                },
                //'choices_as_values' => true,
                'expanded' => false,
                'multiple' => false,
                'required' => false,
                'label' => 'Group/Role Type',
            ))
            ->add('description', TextareaType::class, array(
                'attr' => array(
                    'class' => 'form-control input-sm',
                    'help' => 'A description of the virtual organization and its aims. This is intended to be a human-readable
                    summary to help users find appropriate VOs'
                ),
                'required' => false
            ))
            ->add('allocated_ressources', ChoiceType::class, array(
                'choices' => $ARChoices,
                'choice_label' => function ($key, $value) {
                    return preg_replace('#^\(ID: \d+\)\s+#', '', $value);
                },
                'choice_value' => function ($key) {
                    return $key;
                },
               // 'choices_as_values' => true,
                'expanded' => false,
                'multiple' => false,
                'required' => true,
                'label' => '% of VO share to be allocated to this group',
            ))
            ->add('is_group_used', CheckboxType::class, array(
                'required' => false,
            ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\VO\VoVomsGroup'
        ));
    }


}
