<?php

namespace App\Form;

use App\Entity\Utilisateur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SecurityPasswordType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les deux mots de passe ne sont pas identiques.',
                'required' => true,
                'first_options'  => array('label' => 'Mot de passe :'),
                'second_options' => array('label' => 'Vérification :'),
            ])
            ->add('hidden', HiddenType::class, [
                'mapped' => false,
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(array(
            'data_class' => Utilisateur::class,
            'validation_groups' => array('pwd'),
        ));
    }
}
