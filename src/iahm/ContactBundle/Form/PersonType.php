<?php

namespace iahm\ContactBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PersonType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', 'text', array(
                'required' => true,
            ))
            ->add('lastname', 'text', array(
                'required' => true,
            ))
            ->add('title', 'text', array(
                'required' => false,
            ))
            ->add('gender', 'text', array(
                'required' => false,
            ))
            ->add('dateOfBirth', 'date', array(
                'required' => false,
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
            ))
            ->add('languages', 'collection', array(
                'required' => false,
            ))
            ->add('events')
            ->add('phones', 'collection', array(
                'required' => false,
                'type' => new PhoneType(),
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false
            ))
            ->add('emails', 'collection', array(
                'required' => false,
                'type' => new EmailType(),
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false
            ))
            ->add('comment_txt', 'text', array(
                'required' => false,
            ))
            ->add('type', 'text', array(
                'required' => false,
            ));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'iahm\ContactBundle\Entity\Person',
            'csrf_protection' => false
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'contact';
    }
}
