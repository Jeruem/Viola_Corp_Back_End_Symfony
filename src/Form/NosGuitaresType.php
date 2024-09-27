<?php

// src/Form/NosGuitaresType.php

namespace App\Form;

use App\Entity\NosGuitares;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NosGuitaresType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('description')
            ->add('image', FileType::class, [
                'label' => 'Image (PNG, JPG, JPEG)',
                'mapped' => false, // Cela signifie que ce champ ne sera pas directement lié à l'entité
                'required' => false, // Le téléchargement d'image est optionnel
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => NosGuitares::class,
        ]);
    }
}

