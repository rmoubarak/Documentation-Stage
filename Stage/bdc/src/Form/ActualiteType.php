<?php

namespace App\Form;

use App\Entity\Actualite;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ActualiteType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('statut', ChoiceType::class, [
                'label' => 'Statut* :',
                'choices' => Actualite::STATUTS,
                'label_attr' => ['class' => 'radio-inline'],
                'choice_label' => function ($choice, $key, $value) {
                    return $value;
                },
                'expanded' => true,
                'required' => true,
            ])
            ->add('titre', null, [
                'label' => 'Titre* :',
                'attr' => ['placeholder' => ''],
                'row_attr' => ['class' => 'form-floating',],
                'required' => true,
            ])
            ->add('libelle', null, [
                'label' => 'Détail* :',
                'attr' => ['rows' => 20],
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
            'data_class' => Actualite::class,
            'label' => false,
        ]);
    }
}
