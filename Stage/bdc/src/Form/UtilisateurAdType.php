<?php

namespace App\Form;

use App\Entity\Utilisateur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UtilisateurAdType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('adUser', null, array(
                'label' => 'Utilisateur* : ',
                'attr' => ['data-autocomplete-ad' => 'true'],
                'mapped' => false,
                'required' => true,
            ))
            ->add('adUserHidden', HiddenType::class, array(
                'mapped' => false,
                'required' => true,
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
        ]);
    }
}
