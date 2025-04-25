<?php

namespace App\Form;

use App\Entity\Direction;
use App\Entity\Pole;
use App\Entity\Utilisateur;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UtilisateurType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $utilisateur = $builder->getData();

        $builder
            ->add('id', HiddenType::class)
            ->add('pole', EntityType::class, array(
                'label' => 'Pôle* :',
                'class' => Pole::class,
                'query_builder' => function(EntityRepository $er) use ($utilisateur) {
                    // si le pole est désactivé, on le sélectionne quand même en modification
                    if ($utilisateur->getDirection() && $utilisateur->getDirection()->getPole()->getActif() == false) {
                        return $er->createQueryBuilder('p')
                            ->where('p.actif = true OR p.id = :pole_id')
                            ->setParameter('pole_id', $utilisateur->getDirection()->getPole()->getId());
                    } else {
                        return $er->createQueryBuilder('p')
                            ->where('p.actif = true');
                    }
                },
                'mapped' => false,
                'required' => true,
            ))
            ->add('login', null, [
                'label' => 'Identifiant*',
                'attr' => ['placeholder' => ''],
                'row_attr' => ['class' => 'form-floating',],
                'required' => true,
            ])
            ->add('civilite', ChoiceType::class, [
                'label' => 'Civilité* :',
                'choices' => [
                    'Mme' => 'Mme',
                    'M.' => 'M.',
                ],
                'label_attr' => ['class' => 'radio-inline'],
                'expanded' => true,
                'required' => true,
            ])
            ->add('nom', null, [
                'label' => 'Nom*',
                'row_attr' => ['class' => 'form-floating'],
                'attr' => ['placeholder' => ''],
                'required' => true,
            ])
            ->add('prenom', null, [
                'label' => 'Prénom*',
                'row_attr' => ['class' => 'form-floating'],
                'attr' => ['placeholder' => ''],
                'required' => true,
            ])
            ->add('role', ChoiceType::class, [
                'label' => 'Rôle* : ',
                'placeholder' => 'NR',
                'choices' => Utilisateur::ROLES,
                'label_attr' => ['class' => 'radio-inline'],
                'choice_label' => function ($choice, $key, $value) {
                    return $value;
                },
                'expanded' => true,
                'required' => true,
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email*',
                'row_attr' => ['class' => 'form-floating'],
                'attr' => ['placeholder' => ''],
                'required' => true,
            ])
            ->add('telephone', null, [
                'label' => 'Téléphone',
                'row_attr' => ['class' => 'form-floating'],
                'attr' => ['placeholder' => ''],
                'required' => false,
            ])
            ->add('matricule', null, [
                'label' => 'Matricule :',
                'row_attr' => ['class' => 'form-floating'],
                'attr' => ['placeholder' => ''],
                'required' => false,
            ])
            ->add('actif', ChoiceType::class, [
                'label' => 'Actif* ?',
                'choices' => array_flip(Utilisateur::BOOLS),
                'label_attr' => ['class' => 'radio-inline'],
                'expanded' => true,
                'required' => true,
            ])
            ->add('showMenu', ChoiceType::class, [
                'label' => 'Afficher le menu par défaut* ?',
                'choices' => array_flip(Utilisateur::BOOLS),
                'label_attr' => ['class' => 'radio-inline'],
                'expanded' => true,
                'required' => true,
            ])
        ;

        $formModifier = function (FormInterface $form, Pole $pole = null) use ($utilisateur) {
            $directions = null === $pole ? [] : $pole->getActivesDirections($utilisateur);

            $form->add('direction', EntityType::class, [
                'label' => 'Direction* :',
                'class' => Direction::class,
                'choices' => $directions,
            ]);
        };

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($formModifier) {
                // this would be your entity, i.e. PoleMeetup
                $data = $event->getData();

                $formModifier($event->getForm(), $data->getPole());
            }
        );

        $builder->get('pole')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) use ($formModifier) {
                // It's important here to fetch $event->getForm()->getData(), as
                // $event->getData() will get you the client data (that is, the ID)
                $pole = $event->getForm()->getData();

                // since we've added the listener to the child, we'll have to pass on
                // the parent to the callback functions!
                $formModifier($event->getForm()->getParent(), $pole);
            }
        );
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
