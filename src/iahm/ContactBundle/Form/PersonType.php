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
            ->add('firstname')
            ->add('lastname')
            ->add('title')
            ->add('gender')
            ->add('dateOfBirth', 'date', array(
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
            ))
            ->add('languages')
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
            ->add('comment_txt')
            ->add('type');
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
