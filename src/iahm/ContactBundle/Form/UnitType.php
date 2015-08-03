<?php

namespace iahm\ContactBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UnitType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text', array(
                'description' => 'The title of the Group',
            ))
            ->add('event', 'choice', array(
                'description' => 'Event where the Group is',
                'required' => false,
            ))
            ->add('entities', 'collection', array(
                'description' => 'Entities members of the Group',
                'required' => false,
            ))
            ->add('members', 'collection', array(
                'description' => 'Memebers of the Group',
                'required' => false,
            ))
            ->add('parent', 'choice', array(
                'description' => 'Parent Group of the Group',
                'required' => false,
            ))
            ->add('childrens', 'collection', array(
                'description' => 'Subgroups of the Group',
                'required' => false,
                'type' => new UnitSimpleType(),
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false
            ))
            ->add('leaders', 'collection', array(
                'description' => 'Leaders of the Group',
                'required' => false,
            ));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'iahm\ContactBundle\Entity\Unit',
            'csrf_protection' => false
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'group';
    }
}
