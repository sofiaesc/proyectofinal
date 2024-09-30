<?php

namespace App\Form;

use App\Entity\Usuario;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

class UsuarioType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', null, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Email(),
                    new UniqueEntity([
                        'fields' => 'email',
                        'message' => 'Este correo ya está en uso.',
                    ]),
                ],
            ])
            ->add('password', null, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length([
                        'min' => 8, // Cambiado a 8 caracteres mínimos
                        'minMessage' => 'La contraseña debe tener al menos {{ limit }} caracteres.',
                    ]),
                    new Assert\Regex([
                        'pattern' => '/[A-Z]/',
                        'message' => 'La contraseña debe contener al menos una letra mayúscula.',
                    ]),
                    new Assert\Regex([
                        'pattern' => '/[a-z]/',
                        'message' => 'La contraseña debe contener al menos una letra minúscula.',
                    ]),
                    new Assert\Regex([
                        'pattern' => '/[0-9]/',
                        'message' => 'La contraseña debe contener al menos un número.',
                    ]),
                    new Assert\Regex([
                        'pattern' => '/[\W_]/',
                        'message' => 'La contraseña debe contener al menos un carácter especial.',
                    ]),
                ],
            ])
            ->add('nombre', null, [
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])
            ->add('apellido', null, [
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])
            ->add('dni', null, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new UniqueEntity([
                        'fields' => 'dni',
                        'message' => 'Este DNI ya está en uso.',
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Usuario::class,
        ]);
    }
}
