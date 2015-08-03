<?php

namespace iahm\ContactBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DonationType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date', 'datetime', array(
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd HH:mm:ss',
            ))
            ->add('amount')
            ->add('currency')
            ->add('type')
            ->add('person')
            ->remove('comment')
            ->add('comment_txt', 'text')
            ->add('entity')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'iahm\ContactBundle\Entity\Donation',
            'csrf_protection' => false
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'donation';
    }
}
