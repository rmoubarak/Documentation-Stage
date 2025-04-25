<?php

namespace App\Form;

use App\Entity\Structure;
use App\Entity\Utilisateur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UtilisateurPasswordType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('civilite', ChoiceType::class, [
                'label' => 'Civilité* : ',
                'choices' => Utilisateur::CIVILITES,
                'label_attr' => ['class' => 'radio-inline'],
                'choice_label' => function ($choice, $key, $value) {
                    return $value;
                },
                'expanded' => true,
                'required' => true,
            ])
            ->add('nom', null, [
                'attr' => ['autofocus' => true, 'placeholder' => ''],
                'row_attr' => ['class' => 'form-floating',],
                'label' => 'Nom* :',
                'required' => true,
            ])
            ->add('prenom', null, [
                'label' => 'Prénom* :',
                'attr' => ['placeholder' => ''],
                'row_attr' => ['class' => 'form-floating',],
                'required' => true,
            ])
            ->add('email', EmailType::class, [
                'label' => 'Adresse email* :',
                'attr' => ['placeholder' => ''],
                'row_attr' => ['class' => 'form-floating',],
                'required' => true,
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les deux mots de passe ne sont pas identiques.',
                'first_options'  => array('label' => 'Mot de passe :'),
                'second_options' => array('label' => 'Vérification :'),
                'required' => true,
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
            'validation_groups' => array('Default', 'pwd'),
        ]);
    }
}
