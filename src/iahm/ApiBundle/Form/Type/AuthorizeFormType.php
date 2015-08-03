<?php

namespace iahm\ApiBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AuthorizeFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('allowAccess', 'checkbox', array(
            'label' => 'Allow access',
        ));
    }

    public function getDefaultOptions(array $options)
    {
        return array('data_class' => 'iahm\ApiBundle\Form\Model\Authorize');
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver){
        $resolver->setDefaults(array('data_class' => 'iahm\ApiBundle\Form\Model\Authorize'));
    }


    public function getName()
    {
        return 'iahm_api_authorize';
    }

}