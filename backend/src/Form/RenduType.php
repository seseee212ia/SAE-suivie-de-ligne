<?php

namespace App\Form;

use App\Entity\Etudiants;
use App\Entity\Groupe;
use App\Entity\Rendu;
use App\Entity\Sae;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class RenduType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class)
            ->add('fileImg', FileType::class, [
                'mapped' => false,
                'required' => true,
                'constraints' => [
                    new File([
                        'maxSize' => '10M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/webp'
                        ]
                    ])
                ],
            ])
            ->add('date', DateType::class, [
                'widget' => 'single_text',
            ])
            ->add('sae', EntityType::class, [
                'class' => Sae::class,
                'choice_label' => 'titre',
            ])
            ->add('etudiant', EntityType::class, [
                'class' => Etudiants::class,
                'choice_label' => 'nom',
            ])
            ->add('groupe', EntityType::class, [
                'class' => Groupe::class,
                'choice_label' => 'nom',
                'required' => false,
            ])
            ->add('file', FileType::class, [
                'mapped' => false,
                'required' => true,
                'constraints' => [
                    new File([
                        'maxSize' => '10M',
                    ])
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Rendu::class
        ]);
    }
}