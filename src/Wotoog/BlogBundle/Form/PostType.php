<?php

namespace Wotoog\BlogBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PostType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('visibility', 'genemu_jqueryselect2_choice', array(
                'choices' => array(
                    'private' => 'privée',
                    'public' => 'public',
                )
            ))
            ->add('title')
            ->add('category')
            ->add('content', 'textarea', array(
                'attr' => array(
                    'class' => 'tinymce',
                    'data-theme' => 'blog'
                )))
            ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Wotoog\BlogBundle\Entity\Post'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'wotoog_blogbundle_post';
    }
}
