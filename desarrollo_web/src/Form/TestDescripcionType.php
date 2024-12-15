<?php

namespace App\Form;

use App\Entity\Test;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints\Length;

class TestDescripcionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('descripcion', TextareaType::class, [
                'label' => false,
                'required' => false,
                'constraints' => [
                    new Length([
                        'max' => 5000,
                        'maxMessage' => 'La descripción no puede tener más de {{ limit }} caracteres.',
                    ]),
                ],
                'attr' => [
                    'class' => 'descripcion-input', // Aquí se agrega el atributo class
                    'placeholder' => 'Agregue una descripción...', // Coloca el placeholder dentro de 'attr'
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Test::class,
        ]);
    }
}
