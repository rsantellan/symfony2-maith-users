<?php

namespace Maith\Common\UsersBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', 'email', array('label' => 'form.email', 'translation_domain' => 'FOSUserBundle'))
            ->add('fullName')
            //->add('user_roles')
            ->add('user_groups', null, array(
                  'required' => false,
                  'multiple' => true, 
                  'expanded' => true
                ))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Maith\Common\UsersBundle\Entity\User'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'maith_userbundle_user';
    }
}
