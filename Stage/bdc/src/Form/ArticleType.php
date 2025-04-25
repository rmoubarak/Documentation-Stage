<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Categorie;
use Doctrine\DBAL\Types\TextType;
use Dom\Entity;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class ArticleType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', null, [
                'label' => 'Titre* :',
                'required' => true,
            ])
            ->add('categories', null, [
                'label' => 'Categorie(s)* :',
                'placeholder' => 'Choisissez une categorie',
                'required' => true,
            ])
            ->add('detail', TextareaType::class, [
                'label' => 'Détail* :',
                'required' => true,
            ])
            ->add('url', null, [
                'label' => 'Lien :',
            ]);
    }
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
            'label' => false,
        ]);
    }
}

