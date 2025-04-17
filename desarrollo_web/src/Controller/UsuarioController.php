<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route; 
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface; 
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Form\FormError;
use App\Form\UsuarioEditType;
use App\Form\UsuarioType;
use App\Entity\Usuario;
use Exception;

class UsuarioController extends AbstractController
{
    #[Route('/new_user', name: 'app_new_user')]
    public function new_user(Request $request, 
                             EntityManagerInterface $entityManager, 
                             UserPasswordHasherInterface $passwordHasher): Response    {
        $usuario = new Usuario();
        $form = $this->createForm(UsuarioType::class, $usuario);

        // Procesar el formulario con la solicitud HTTP
        $form->handleRequest($request);
    
        // Verificar si el formulario fue enviado y es válido
        if ($form->isSubmitted() && $form->isValid()) {

            // Hashear la contraseña antes de guardarla
            $hashedPassword = $passwordHasher->hashPassword(
                $usuario,
                $usuario->getPassword() // Obtiene la contraseña en texto plano del objeto Usuario
            );
            $usuario->setPassword($hashedPassword); // Establece la contraseña hasheada
            $roles = ["ROLE_USER"];
            $usuario->setRoles($roles);
            // Guardar la entidad en la base de datos
            $entityManager->persist($usuario);
            $entityManager->flush();

            return $this->redirectToRoute('app_index');
        }

        return $this->render('front/user/new_user.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response {
        // Obtén el error de inicio de sesión, si hay uno
        $error = $authenticationUtils->getLastAuthenticationError();

        // Último nombre de usuario ingresado (puede ser útil para el formulario)
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('front/user/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(){}

    #[Route('/edit_profile', name: 'app_edit_profile')]
    public function edit_profile(Request $request, 
                                 EntityManagerInterface $entityManager,
                                 UserPasswordHasherInterface $passwordHasher): Response
    {
        // Obtener el usuario autenticado
        $usuario = $this->getUser();

        if (!$usuario) {
            return $this->redirectToRoute('app_login');
        }

        // Guardamos los valores originales antes del handleRequest
        $emailOriginal = $usuario->getEmail();
        $nombreOriginal = $usuario->getNombre();
        $apellidoOriginal = $usuario->getApellido();
        // Podés agregar más campos si necesitás

        $form = $this->createForm(UsuarioEditType::class, $usuario);
        $form->handleRequest($request);

        $mensaje_resultado = "";
        $color_resultado = null;

        if ($form->isSubmitted()) {
            $actPassword = $form->get('actualPassword')->getData();
            $newPassword = $form->get('password')->getData();

            if (!$form->isValid()) {
                // Restauramos todos los campos originales si cualquier constraint falla
                $usuario->setEmail($emailOriginal);
                $usuario->setNombre($nombreOriginal);
                $usuario->setApellido($apellidoOriginal);
                
                $color_resultado = "#eb3017";
                $mensaje_resultado = "Error al guardar los datos. Verificá los campos.";
            } elseif (!$passwordHasher->isPasswordValid($usuario, $actPassword)) {
                // También restauramos si la contraseña es incorrecta
                $usuario->setEmail($emailOriginal);
                $usuario->setNombre($nombreOriginal);
                $usuario->setApellido($apellidoOriginal);

                $color_resultado = "#eb3017";
                $mensaje_resultado = "La contraseña ingresada es incorrecta. Intente nuevamente.";
            } else {
                if ($newPassword) {
                    $hashedPassword = $passwordHasher->hashPassword($usuario, $newPassword);
                    $usuario->setPassword($hashedPassword);
                }

                $entityManager->flush();

                $color_resultado = "#47db1a";
                $mensaje_resultado = "¡Éxito! La información se guardó correctamente.";
            }
        }

        return $this->render('front/user/edit_profile.html.twig', [
            'form' => $form->createView(),
            'mensaje_resultado' => $mensaje_resultado,
            'color_resultado' => $color_resultado 
        ]);

    }
}