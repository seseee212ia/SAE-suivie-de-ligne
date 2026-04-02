<?php

namespace App\Form;

use App\Entity\Documents;
use App\Entity\Enseignants;
use App\Entity\Sae;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class DocumentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class)
            ->add('sae', EntityType::class, [
                'class' => Sae::class,
                'choice_label' => 'id',
            ])
            ->add('enseignant', EntityType::class, [
                'class' => Enseignants::class,
                'choice_label' => 'id',
            ])
            ->add('file', FileType::class, [
                'mapped' => false,
                'required' => true,
                'constraints' => [
                    new File(['maxSize' => '10M'])
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Documents::class
        ]);
    }
}
