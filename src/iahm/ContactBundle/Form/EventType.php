<?php

namespace iahm\ContactBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EventType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('title')
            ->add('start', 'datetime', array(
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd HH:mm:ss',
            ))
            ->add('end', 'datetime', array(
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd HH:mm:ss',
            ))
            ->add('persons')
            ->add('parent')
            ->add('childrens', 'collection', array(
                'required' => false,
                'type' => new EventSimpleType(),
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false
            ))
            ->add('groups', 'collection', array(
                'required' => false,
                'type' => new UnitToEventType(),
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false
            ));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'iahm\ContactBundle\Entity\Event',
            'csrf_protection' => false
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'event';
    }
}
