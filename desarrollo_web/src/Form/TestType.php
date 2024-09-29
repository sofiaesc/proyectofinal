<?php

namespace App\Form;

use App\Entity\Test;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Validator\Constraints\File;

class TestType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('foto', FileType::class, [
                'label' => false,
                'required' => true,
                'constraints' => [
                    new File([
                        'maxSize' => '10M', // Limita el tamaño del archivo
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Por favor sube una imagen válida (JPEG o PNG)',
                    ])
                ],
            ])
            // Campos ocultos para las coordenadas
            ->add('x1', HiddenType::class, ['mapped' => false])
            ->add('y1', HiddenType::class, ['mapped' => false])
            ->add('x2', HiddenType::class, ['mapped' => false])
            ->add('y2', HiddenType::class, ['mapped' => false]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Test::class,
        ]);
    }
}
