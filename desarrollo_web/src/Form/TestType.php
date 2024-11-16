<?php

namespace App\Form;

use App\Entity\Test;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;

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
                        'maxSize' => '10M',
                        'mimeTypes' => ['image/jpeg', 'image/png'],
                        'mimeTypesMessage' => 'Por favor sube una imagen válida (JPEG o PNG)',
                    ])
                ],
                'mapped' => false,
            ])
            ->add('nombre_alt', TextType::class, [
                'label' => 'Identificador',
                'required' => true,
                'label_attr' => ['class' => 'text-left'],
                'constraints' => [
                    new NotBlank(['message' => 'Debe ingresar un identificador para el test.']),
                ],
            ])
            ->add('x1', HiddenType::class, ['mapped' => false])
            ->add('y1', HiddenType::class, ['mapped' => false])
            ->add('x2', HiddenType::class, ['mapped' => false])
            ->add('y2', HiddenType::class, ['mapped' => false])
            ->add('pocillos_hab', HiddenType::class); 
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Test::class, // Define tu entidad relacionada.
        ]);
    }
}
