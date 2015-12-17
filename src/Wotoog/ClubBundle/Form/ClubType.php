<?php

namespace Wotoog\ClubBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ClubType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array('required' => true))
            ->add('description', 'textarea', array('required' => true))
            ->add('visibility', 'hidden', array('required' => true))
            ->add('theme', 'radio', array('required' => true))
            ->add('extension', 'checkbox', array('required' => true))
            ->add('picture', 'file', array('required' => true, 'label' => 'Affectez une image de profil au club'))
            ->add('theme', 'choice',  array('label' => 'Veuillez choisir un theme à adopter pour votre club', 'choices' => $this->getThemeList(), 'required' => true, 'multiple' => false))
            ->add('extension', 'choice', array(
                    'choices'     => $this->getExtensionList(),
                    'required'  => true,
                    'multiple' => true,
                    'expanded' => true,
                ))
            ->add('cover', 'file', array('label' => 'Affectez une image de couverture au club', 'required' => true))
        ;
    }

    /** @todo make it dynamic
     * get the list of available themes
     * @return array
     */
    public function getThemeList(){
        $choices = array(
            'default' => 'Par défaut',
            'Amelia' => 'Sweet and cheery',
            'Cerulean' => 'A calm blue sky',
            'Cosmo' => 'An ode to Metro',
            'Cyborg' => 'Jet black and electric blue',
            'Darkly' => 'Flatly in night mode',
            'Flatly' => 'Flat and modern',
            'Journal' => 'Crisp like a new sheet of paper',
            'Lumen' => 'Light and shadow',
            'Readable' => 'Optimized for legibility',
            'Simplex' => 'Slate',
            'Spacelab' => 'Silvery and sleek',
            'Superhero' => 'The brave and the blue',
            'United' => 'Ubuntu orange and unique font',
            'Yeti' => 'A friendly foundation',
        );
        return $choices;
    }

    public function getExtensionList(){
        $choices = array(
            'mail' => 'Messagerie',
            'timeline' => 'Timeline',
            'talk' => 'Discussion',
            'faq' => 'Centre de questions',
            'wiki' => 'Wiki',
            'blog' => 'Blog',
            'agenda' => 'Agenda',
        );
        return $choices;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Wotoog\ClubBundle\Entity\Club'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'wotoog_clubbundle_club';
    }
}
