<?php

namespace App\Form;

use App\Entity\Usuario;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormError;

class UsuarioEditType extends AbstractType
{
    private $validator;

    // Inyectamos el validador
    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'El correo no puede estar vacío.',
                    ]),
                    new Assert\Email([
                        'message' => 'Por favor, ingrese un correo electrónico válido.',
                    ]),
                ],
                'attr' => ['class' => 'no-border']
            ])
            ->add('nombre', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'El nombre no puede estar vacío.',
                    ]),
                ],
                'attr' => ['class' => 'no-border']
            ])
            ->add('apellido', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'El apellido no puede estar vacío.',
                    ]),
                ],
                'attr' => ['class' => 'no-border']
            ])
            ->add('password', PasswordType::class, [
                'required' => false,
                'mapped' => false,
                'label' => 'Contraseña nueva',
                'attr' => [
                    'class' => 'no-border',
                    'placeholder' => 'Opcional'
                ]
            ])
            ->add('confirmNewPassword', PasswordType::class, [
                'required' => false,
                'mapped' => false,
                'label' => 'Repita la contraseña nueva',
                'attr' => [
                    'class' => 'no-border',
                    'placeholder' => 'Opcional'
                ]
            ])
            ->add('actualPassword', TextType::class, [
                'mapped' => false,
                'label' => 'Contraseña actual',
                'attr' => ['class' => 'no-border']
            ]);

        // Verificar si las contraseñas coinciden en el POST_SUBMIT
        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
            $form = $event->getForm();
            $newPassword = $form->get('password')->getData();
            $confirmPassword = $form->get('confirmNewPassword')->getData();

            // Solo validar si al menos uno de los campos no está vacío
            if (!empty($newPassword) || !empty($confirmPassword)) {
                // Validar si las contraseñas coinciden
                if ($newPassword !== $confirmPassword) {
                    $form->get('confirmNewPassword')->addError(
                        new FormError('Las contraseñas no coinciden.')
                    );
                }

                // Aplicar las constraints de la contraseña
                $passwordConstraints = [
                    new Assert\Length([
                        'min' => 8,
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
                    ])
                ];

                // Validar el nuevo valor de la contraseña
                $violations = $this->validator->validate($newPassword, $passwordConstraints);

                // Añadir errores si las restricciones no se cumplen
                foreach ($violations as $violation) {
                    $form->get('password')->addError(new FormError($violation->getMessage()));
                }
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Usuario::class,
        ]);
    }
}

